<?php

namespace App\Charts;

class PieChart extends PieOrDoughnutChart
{
    public function __construct($config)
    {
        parent::__construct($config);

        $this->type('pie')
            ->ratio(1);
    }
}
