<?php

namespace machour\yii2\prometheus\models;

use yii\base\Model;

class Metric extends Model
{
    /**
     * @var string The metric name
     */
    public $name;
    /**
     * @var int|float The metric value
     */
    public $value;
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $description;
    /**
     * @var array
     */
    public $labels;

    public function rules()
    {
        return [
            ['name', 'match', 'pattern' => '!^[-_a-z]+$!'],
            ['value', 'number'],
            ['description', 'string'],
        ];
    }

    /**
     * Exports a counter metric
     *
     * @see https://prometheus.io/docs/concepts/metric_types/#counter
     */
    public static function counter($name, $value, array $labels = [], $description = null)
    {
        return self::metric('counter', $name, $value, $labels, $description);
    }

    /**
     * Exports a gauge metric
     *
     * @see https://prometheus.io/docs/concepts/metric_types/#gauge
     */
    public static function gauge($name, $value, array $labels = [], $description = null)
    {
        return self::metric('gauge', $name, $value, $labels, $description);
    }

    /**
     * @param string $type The metric type
     * @param string $name The metric name
     * @param float|int $value The metric value
     * @param array $labels Array of key => value pairs
     * @param string|null $description An optional description for the metric
     * @return static
     */
    private static function metric($type, $name, $value, array $labels = [], $description = null)
    {
        return new static([
            'name' => $name,
            'value' => $value,
            'labels' => $labels,
            'description' => $description,
            'type' => $type,
        ]);
    }
}