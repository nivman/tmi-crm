<?php

namespace App\Charts;

class DoughnutChart extends PieOrDoughnutChart
{
    public function __construct($config)
    {
        parent::__construct($config);

        $this->type('doughnut')
            ->ratio(1);
    }
}
