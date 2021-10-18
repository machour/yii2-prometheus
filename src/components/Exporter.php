<?php

namespace machour\yii2\prometheus\components;

use machour\yii2\prometheus\exceptions\ExporterException;
use machour\yii2\prometheus\models\Metric;
use yii\base\Component;

class Exporter extends Component
{
    /**
     * @var string Node exporter's textfile collector directory
     */
    public $collectorDir = "/var/lib/node_exporter/textfile_collector/";

    /**
     * @var string Default metrics file name
     */
    public $defaultFile = 'yii2-prometheus-exporter.prom';

    /**
     * Exports a metric to the specified file, or the default file.
     *
     * @param Metric $metric The metric to export.
     * @param ?string $file The filename. The .prom extension will be automatically appended if not present.
     * @return bool
     * @throws ExporterException
     */
    public function exportMetric(Metric $metric, $file = null)
    {
        return $this->exportMetrics([$metric], $file);
    }

    /**
     * Exports an array of metrics to the specified file, or the default file.
     *
     * @param Metric[] $metrics Array of metrics to export
     * @param ?string $file The filename. The .prom extension will be automatically appended if not present.
     * @return bool
     * @throws ExporterException
     */
    public function exportMetrics(array $metrics, $file = null)
    {
        if (is_null($file)) {
            $file = $this->defaultFile;
        } elseif (substr($file, -4) !== '.prom') {
            $file .= '.prom';
        }

        $buffer = '';
        foreach ($metrics as $metric) {
            if (!$metric->validate()) {
                throw new ExporterException("Invalid metric: " . var_export($metric->errors, 1));
            }

            $name = $metric->name;

            $labels = [];
            foreach ($metric->labels as $label => $value) {
                $labels[] = "$label=\"$value\",";
            }
            $labels = '{' . join(',', $labels) . '}';

            if ($labels === '{}') {
                $labels = '';
            }

            $buffer .= "#HELP $name $metric->description\n";
            $buffer .= "#TYPE $name $metric->type\n";
            $buffer .= "$name$labels $metric->value\n";
        }

        $destination = "$this->collectorDir$file";
        $tempFile = "$destination$$";

        if (!@file_put_contents($tempFile, $buffer)) {
            throw new ExporterException("Could not write to temporary file $tempFile");
        }

        if (!@rename($tempFile, $destination)) {
            throw new ExporterException("Could not rename temporary file $tempFile");
        }

        return true;
    }
}