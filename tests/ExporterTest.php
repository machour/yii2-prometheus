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

        $this->assertTrue(
            $exporter->exportMetric(
                Metric::gauge('metric_name', 34, [], 'The metric description')
            )
        );

        $outFile = $exporter->collectorDir . $exporter->defaultFile;
        $this->assertFileEquals(__DIR__ . '/data/yii2-prometheus-exporter.prom', $outFile);
    }

    public function testExportGauge()
    {
        $exporter = $this->getExporter();

        $this->assertTrue($exporter->exportMetric(
            Metric::gauge('pi', 3.1415, [], 'My gauge'),
            'test_gauge',
        ));

        $outFile = $exporter->collectorDir . 'test_gauge.prom';
        $this->assertFileEquals(__DIR__ . '/data/test_gauge.prom', $outFile);
    }

    public function testExportMultiple()
    {
        $exporter = $this->getExporter();

        $this->assertTrue($exporter->exportMetrics([
            Metric::gauge('pi', 3.1415, [], 'My gauge'),
            Metric::counter('bugs', 42, [], 'My counter'),
            ], 'test_multiple',
        ));

        $outFile = $exporter->collectorDir . 'test_multiple.prom';
        $this->assertFileEquals(__DIR__ . '/data/test_multiple.prom', $outFile);
    }

    public function testBadMetric()
    {
        $exporter = $this->getExporter();
        $this->expectException('\machour\yii2\prometheus\exceptions\ExporterException');

        $exporter->exportMetric(
            Metric::gauge('Woo ps', 34, [], 'The metric description'),
            'test_metric',
        );
    }

    public function testFileFailure()
    {
        $exporter = $this->getExporter('/non/existent/dir/');
        $this->expectException('\machour\yii2\prometheus\exceptions\ExporterException');

        $exporter->exportMetric(
            Metric::gauge('metric_name', 34, [], 'The metric description'),
            'test_metric',
        );
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
