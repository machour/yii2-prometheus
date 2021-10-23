<?php

namespace machourunit\yii2\prometheus;

use machour\yii2\prometheus\components\Exporter;
use machour\yii2\prometheus\models\Metric;
use PHPUnit\Framework\TestCase;

final class ExporterTest extends TestCase
{
    public function testExport()
    {
        $exporter = $this->getExporter();

        $gauge = Metric::gauge('metric_name', 'The metric description');
        $gauge->addMeasurement(34);

        $this->assertTrue($exporter->exportMetric($gauge));

        $outFile = $exporter->collectorDir . $exporter->defaultFile;
        $this->assertFileEquals(__DIR__ . '/data/yii2-prometheus-exporter.prom', $outFile);
    }

    public function testExportGauge()
    {
        $exporter = $this->getExporter();

        $gauge = Metric::gauge('pi', 'My gauge');
        $gauge->addMeasurement(3.1415);

        $this->assertTrue($exporter->exportMetric($gauge,
            'test_gauge'
        ));

        $outFile = $exporter->collectorDir . 'test_gauge.prom';
        $this->assertFileEquals(__DIR__ . '/data/test_gauge.prom', $outFile);
    }

    public function testExportLabels()
    {
        $exporter = $this->getExporter();

        $gauge = Metric::gauge('pi', 'My gauge');
        $gauge->addMeasurement(3.1415, ['foo' => 'bar']);
        $this->assertTrue($exporter->exportMetric($gauge, 'test_labels_one'));

        $outFile = $exporter->collectorDir . 'test_labels_one.prom';
        $this->assertFileEquals(__DIR__ . '/data/test_labels_one.prom', $outFile);

        $gauge = Metric::gauge('pi', 'My gauge');
        $gauge->addMeasurement(3.1415, ['baz' => 'cux', 'foo' => 'bar']);

        $this->assertTrue($exporter->exportMetric(
            $gauge,
            'test_labels_two'
        ));

        $outFile = $exporter->collectorDir . 'test_labels_two.prom';
        $this->assertFileEquals(__DIR__ . '/data/test_labels_two.prom', $outFile);
    }

    public function testExportMultiple()
    {
        $exporter = $this->getExporter();

        $gauge = Metric::gauge('pi', 'My gauge');
        $gauge->addMeasurement(3.1415);

        $counter = Metric::counter('bugs', 'My counter');
        $counter->addMeasurement(42);

        $this->assertTrue($exporter->exportMetrics([$gauge, $counter], 'test_multiple'));

        $outFile = $exporter->collectorDir . 'test_multiple.prom';
        $this->assertFileEquals(__DIR__ . '/data/test_multiple.prom', $outFile);
    }

    public function testBadMetric()
    {
        $exporter = $this->getExporter();
        $this->expectException('\machour\yii2\prometheus\exceptions\ExporterException');

        $gauge = Metric::gauge('Woo ps',  'The metric description');
        $gauge->addMeasurement(34);
        $exporter->exportMetric($gauge, 'test_metric');
    }

    public function testFileFailure()
    {
        $exporter = $this->getExporter('/non/existent/dir/');
        $this->expectException('\machour\yii2\prometheus\exceptions\ExporterException');

        $gauge = Metric::gauge('Woo ps',  'The metric description');
        $gauge->addMeasurement(34);
        $exporter->exportMetric($gauge, 'test_metric');
    }

    /**
     * @param ?string $collectorDir
     * @return Exporter
     */
    private function getExporter($collectorDir = null)
    {
        if (is_null($collectorDir)) {
            $collectorDir = __DIR__ . '/output/';
        }

        return new Exporter([
            'collectorDir' => $collectorDir,
        ]);
    }

}
