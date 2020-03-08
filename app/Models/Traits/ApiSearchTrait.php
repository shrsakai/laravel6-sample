<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Trait ApiSearchTrait
 * @package App\Models\Traits
 *
 * 検索で頻出するスコープ関数を集約する
 *
 * @method Builder orderByParamOrder(Collection | array $column, string $input)
 * @method Builder whereBool(string $column, string $value)
 * @method Builder whereFromTo(string $column, string $valueFrom, string $valueTo)
 * @method Builder whereContainsAny(string | Collection | array $column, string $value)
 * @method Builder whereContainsAll(string | Collection | array $column, string $value)
 * @method Builder whereStartsWithAny(string | Collection | array $column, string $value)
 * @method Builder whereStartsWithAll(string | Collection | array $column, string $value)
 */
trait ApiSearchTrait
{
    /**
     * {key}_{order} 形式のソート指定を解釈する
     *
     * @param Builder    $query
     * @param Collection $params コレクション(key -> {key}, value -> {検索時のカラム名})
     * @param string     $input {key}_{order} の文字列
     * @return Builder
     */
    public function scopeOrderByParamOrder(Builder $query, $params, $input)
    {
        if ($input) {
            $paramToColumn = collect($params);
            $paramToColumn->keys()->crossJoin(['asc', 'desc'])
                ->each(function ($item) use ($query, $paramToColumn, $input) {
                    list($param, $order) = $item;
                    if ($input === "{$param}_{$order}") {
                        $query->orderByRaw("{$paramToColumn[$param]} IS NULL ASC")
                            ->orderBy($paramToColumn[$param], $order);
                        return false;  // each ループを停止する
                    }
                    return true;
                });
        }
        return $query;
    }

    /**
     * true/false検索
     *
     * @param Builder $query
     * @param string  $value
     * @param string  $column
     * @return Builder
     */
    public function scopeWhereBool(Builder $query, string $column, $value)
    {
        if ($value) {
            if ($value === 'true') {
                $query = $query->where($column, true);
            }
            if ($value === 'false') {
                $query = $query->where($column, false);
            }
        }
        return $query;
    }

    /**
     * from/to
     *
     * @param Builder $query
     * @param string  $valueFrom
     * @param string  $valueTo
     * @param string  $column
     * @return Builder
     */
    public function scopeWhereFromTo(Builder $query, string $column, $valueFrom, $valueTo)
    {
        if ($valueFrom) {
            $query->where($column, '>=', $valueFrom);
        }
        if ($valueTo) {
            $query->where($column, '<=', $valueTo);
        }
        return $query;
    }

    /**
     * like検索 (スペースでor検索)
     *
     * @param Builder      $query
     * @param string|array $column
     * @param string       $value
     * @return Builder
     */
    public function scopeWhereContainsAny(Builder $query, $column, $value)
    {
        // 全角スペースを半角スペースに変換
        $inputStr = trim(preg_replace('/[ |　]+/u', ' ', $value));
        if (strlen($inputStr)) {
            if (is_string($column)) {
                // $column が文字列(カラム名)の場合
                // (col LIKE str1) OR (col LIKE str2)
                $query->where(function (Builder $q) use ($inputStr, $column) {
                    // 半角スペースで文字列を分割し OR 検索
                    collect(explode(' ', $inputStr))->each(function ($str, $key) use ($q, $column) {
                        ($key == 0) ? $q->where($column, 'LIKE', "%{$str}%")
                            : $q->orWhere($column, 'LIKE', "%{$str}%");
                    });
                });
            } else {
                // $column が配列の場合
                // (col1 LIKE str1) OR (col1 LIKE str2) OR (col2 LIKE str1) OR (col2 LIKE str2)
                $query->where(function (Builder $q) use ($inputStr, $column) {
                    collect($column)->crossJoin(collect(explode(' ', $inputStr)))
                        ->each(function ($item, $key) use ($q, $column) {
                            list($col, $str) = $item;
                            ($key == 0) ? $q->where($col, 'LIKE', "%{$str}%")
                                : $q->orWhere($col, 'LIKE', "%{$str}%");
                        });
                });
            }
        }
        return $query;
    }

    /**
     * like検索 (スペースでand検索)
     *
     * @param Builder      $query
     * @param string|array $column
     * @param string       $value
     * @return Builder
     */
    public function scopeWhereContainsAll(Builder $query, $column, $value)
    {
        // 全角スペースを半角スペースに変換
        $inputStr = trim(preg_replace('/[ |　]+/u', ' ', $value));
        if (strlen($inputStr)) {
            if (is_string($column)) {
                // $column が文字列(カラム名)の場合
                // (col LIKE str1) AND (col LIKE str2)
                $query->where(function (Builder $q) use ($inputStr, $column) {
                    // 半角スペースで文字列を分割し AND 検索
                    collect(explode(' ', $inputStr))->each(function ($str) use ($q, $column) {
                        $q->where($column, 'LIKE', "%{$str}%");
                    });
                });
            } else {
                // $column が配列の場合
                // ((col1 LIKE str1) AND (col1 LIKE str2)) OR ((col2 LIKE str1) AND (col2 LIKE str2))
                collect($column)->each(function ($col, $key) use ($inputStr, $query) {
                    if ($key == 0) {
                        $query->where(function (Builder $q) use ($inputStr, $col) {
                            collect(explode(' ', $inputStr))->each(function ($str) use ($q, $col) {
                                $q->where($col, 'LIKE', "%{$str}%");
                            });
                        });
                    } else {
                        $query->orWhere(function (Builder $q) use ($inputStr, $col) {
                            collect(explode(' ', $inputStr))->each(function ($str) use ($q, $col) {
                                $q->where($col, 'LIKE', "%{$str}%");
                            });
                        });
                    }
                });
            }
        }
        return $query;
    }

    /**
     * 前方一致like検索 (スペースでor検索)
     *
     * @param Builder      $query
     * @param string|array $column
     * @param string       $value
     * @return Builder
     */
    public function scopeWhereStartsWithAny(Builder $query, $column, $value)
    {
        // 全角スペースを半角スペースに変換
        $inputStr = trim(preg_replace('/[ |　]+/u', ' ', $value));
        if (strlen($inputStr)) {
            if (is_string($column)) {
                // $column が文字列(カラム名)の場合
                // (col LIKE str1) OR (col LIKE str2)
                $query->where(function (Builder $q) use ($inputStr, $column) {
                    // 半角スペースで文字列を分割し OR 検索
                    collect(explode(' ', $inputStr))->each(function ($str, $key) use ($q, $column) {
                        ($key == 0) ? $q->where($column, 'LIKE', "{$str}%")
                            : $q->orWhere($column, 'LIKE', "{$str}%");
                    });
                });
            } else {
                // $column が配列の場合
                // (col1 LIKE str1) OR (col1 LIKE str2) OR (col2 LIKE str1) OR (col2 LIKE str2)
                $query->where(function (Builder $q) use ($inputStr, $column) {
                    collect($column)->crossJoin(collect(explode(' ', $inputStr)))
                        ->each(function ($item, $key) use ($q, $column) {
                            list($col, $str) = $item;
                            ($key == 0) ? $q->where($col, 'LIKE', "{$str}%")
                                : $q->orWhere($col, 'LIKE', "{$str}%");
                        });
                });
            }
        }
        return $query;
    }

    /**
     * 前方一致like検索 (スペースでand検索)
     *
     * @param Builder      $query
     * @param string|array $column
     * @param string       $value
     * @return Builder
     */
    public function scopeWhereStartsWithAll(Builder $query, $column, $value)
    {
        // 全角スペースを半角スペースに変換
        $inputStr = trim(preg_replace('/[ |　]+/u', ' ', $value));
        if (strlen($inputStr)) {
            if (is_string($column)) {
                // $column が文字列(カラム名)の場合
                // (col LIKE str1) AND (col LIKE str2)
                $query->where(function (Builder $q) use ($inputStr, $column) {
                    // 半角スペースで文字列を分割し AND 検索
                    collect(explode(' ', $inputStr))->each(function ($str) use ($q, $column) {
                        $q->where($column, 'LIKE', "{$str}%");
                    });
                });
            } else {
                // $column が配列の場合
                // ((col1 LIKE str1) AND (col1 LIKE str2)) OR ((col2 LIKE str1) AND (col2 LIKE str2))
                collect($column)->each(function ($col, $key) use ($inputStr, $query) {
                    if ($key == 0) {
                        $query->where(function (Builder $q) use ($inputStr, $col) {
                            collect(explode(' ', $inputStr))->each(function ($str) use ($q, $col) {
                                $q->where($col, 'LIKE', "{$str}%");
                            });
                        });
                    } else {
                        $query->orWhere(function (Builder $q) use ($inputStr, $col) {
                            collect(explode(' ', $inputStr))->each(function ($str) use ($q, $col) {
                                $q->where($col, 'LIKE', "{$str}%");
                            });
                        });
                    }
                });
            }
        }
        return $query;
    }
}
