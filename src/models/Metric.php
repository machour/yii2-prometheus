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
    public $measurements = [];

    public function rules()
    {
        return [
            [['name'], 'required'],
            ['name', 'match', 'pattern' => '!^[-_a-z]+$!'],
            ['description', 'string'],
        ];
    }

    /**
     * Exports a counter metric
     *
     * @see https://prometheus.io/docs/concepts/metric_types/#counter
     */
    public static function counter($name, $description, array $measurements = [])
    {
        return self::metric('counter', $name, $description, $measurements);
    }

    /**
     * Exports a gauge metric
     *
     * @see https://prometheus.io/docs/concepts/metric_types/#gauge
     */
    public static function gauge($name, $description, array $measurements = [])
    {
        return self::metric('gauge', $name, $description, $measurements);
    }

    /**
     * @param string $type The metric type
     * @param string $name The metric name
     * @param string|null $description An optional description for the metric
     * @param array $measurements Array of measurements
     * @return static
     */
    private static function metric($type, $name, $description, array $measurements = [])
    {
        return new static([
            'name' => $name,
            'measurements' => $measurements,
            'description' => $description,
            'type' => $type,
        ]);
    }

    public function addMeasurement($value, array $labels = [])
    {
        $this->measurements[] = [$value, $labels];
    }
}