<?php

namespace App\Charts;

use Illuminate\Support\Arr;

abstract class Chart
{
    private const Opacity = 0.25;

    protected $colors;

    protected $data = [];

    protected $datasets;

    protected $labels;

    protected $options;

    protected $title;

    protected $type;

    public function __construct($config)
    {
        if (isset($config['labels'])) {
            $this->labels = $config['labels'];
        }
        if (isset($config['datasets'])) {
            $this->datasets = $config['datasets'];
        }
        if (isset($config['title'])) {
            $this->title = $config['title'];
        }
        if (isset($config['type'])) {
            $this->type = $config['type'];
        }
        if (isset($config['options'])) {
            $this->options = $config['options'];
        }
        if (isset($config['data'])) {
            $this->data = $config['data'];
        }

        $this->colors();
    }

    abstract protected function build();

    protected function color($index = null)
    {
        $index = $index ?? count($this->data);

        return $this->colors[$index];
    }

    protected function colors()
    {
        if (!$this->colors) {
            $this->colors = array_values(config('charts.colors'));
        }

        return $this->colors;
    }

    protected function hex2rgba($color)
    {
        \Log::info($color);
        $color = substr($color, 1);
        $hex   = [$color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]];
        $rgb   = array_map('hexdec', $hex);

        return 'rgba(' . implode(',', $rgb) . ',' . self::Opacity . ')';
    }

    abstract protected function response();

    protected function ticks()
    {
        $this->options['scales'] = (object) [
            'xAxes' => [(object) ['gridLines' => ['display' => true, 'drawBorder' => true, 'drawOnChartArea' => false], 'ticks' => ['autoSkip' => false, 'maxRotation' => 90]]],
            'yAxes' => [(object) ['gridLines' => ['display' => true, 'drawBorder' => true, 'drawOnChartArea' => true]]],
        ];
        $this->options['layout'] = (object) ['padding' => (object) ['left' => 0, 'right' => 0, 'top' => 5, 'bottom' => 0]];
    }

    protected function type(string $type)
    {
        $this->type = $type;

        return $this;
    }

    public function datasets(array $datasets)
    {
        $this->datasets = $datasets;

        return $this;
    }

    public function get()
    {
        $this->build();

        return $this->response();
    }

    public function labels(array $labels)
    {
        $this->labels = $labels;

        return $this;
    }

    public function ratio(float $ratio)
    {
        $this->options['aspectRatio'] = $ratio;

        return $this;
    }

    public function title(string $title)
    {
        $this->title = $title;

        return $this;
    }
}
