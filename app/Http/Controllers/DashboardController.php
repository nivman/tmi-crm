<?php

namespace App\Http\Controllers;

use App\Helpers\Date;
use App\Income;
use App\Expense;
use App\Invoice;
use App\Payment;
use App\Project;
use App\Purchase;

use App\Task;
use Carbon\Carbon;
use App\Charts\BarChart;
use App\Charts\PieChart;
use App\Charts\LineChart;
use Illuminate\Http\Request;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use function React\Promise\all;

class DashboardController extends Controller
{
    public function hoursPerCategoryHoursBar(Request $request)
    {
        $dates = $request->request->get('rangeDate');
        $dateRangeArr = explode(" אל ", $dates);
        $dateRange = Date::convertDatesRange($dateRangeArr[0], $dateRangeArr[1]);

        $categoriesTime = (new Task)->sumTasksTimeByCategoriesId($dateRange['startDate'],$dateRange['endDate']);
        $categoriesName = array_column($categoriesTime, 'name');

        // if task have no category replace the null
        $pos = array_search(null, $categoriesName);
        if ($pos)
        {
            $categoriesName[$pos] = "ללא קטגוריה";
        }

        $datasets = $this->createCategoriesDatasets($categoriesTime, $categoriesName);

        $config = [
            'title' => "שעות עבודה פר קטגוריה",
            'datasets' =>  $datasets,
            'labels' => [],
            'options' => [
                'responsive' => false,
                'maintainAspectRatio' => false,
                'legend' => ['display' => true, 'position' => 'bottom', 'lineAt' => 15],
                'name' => 'categoriesTime'

            ],

        ];
        $chart = new BarChart($config);

        return $chart->get();

    }

    public function categoryHoursPerProjectBar(Request $request)
    {

        $projectsIds = [];

        if (isset($request->projects)) {
            $projectsIds = $this->getProjectsIds($request);
        }

        $projectsData = count($projectsIds) === 0 ? $this->projectsByDate() : $this->projectsByIds($projectsIds);
        $aggregateProjectData = $this->aggregateProjectData($projectsData);

        $categories = array_map(function ($category) {

            return [$category->category_id => $category->name];
        }, $projectsData);

        $categoriesKeyValue = [];

        $categories = new RecursiveIteratorIterator(new RecursiveArrayIterator($categories));

        foreach ($categories as $key => $value) {
            $categoriesKeyValue[$key] = $value;
        }

        list($groupProjectsData, $categoriesNames) = $this->groupProjectsData($projectsData);

        $groupProjectsData = $this->createFullProjectTimeData($categoriesNames, $groupProjectsData);

        if(count($groupProjectsData) > 1) {
            foreach ($groupProjectsData as $key => $item) {
                usort($groupProjectsData[$key], fn($a, $b) => strcmp($a->category_id, $b->category_id));
            }
        }

        $formatProjectsData = $this->formatProjectsData($groupProjectsData);

        $setLabels = $this->setLabels($groupProjectsData, $categoriesKeyValue, $categoriesNames, $formatProjectsData);

        $config = [
            'title' => "שעות עבודה לפי קטגוריות פר פרוייקט",
            'datasets' => $formatProjectsData,
            'labels' => $setLabels,
            'options' => [
                'responsive' => false,
                'maintainAspectRatio' => false,
                'legend' => ['display' => true, 'position' => 'bottom', 'lineAt' => 15],
                'projectsData' => $aggregateProjectData,
                'name' => 'projectsTime'
            ],

        ];
        $chart = new BarChart($config);

        return $chart->get();
    }

    public function customer()
    {
        $received_payment = Payment::myPayments()->received()->groupBy('payable_id')
            ->selectRaw('sum(amount) as total_amount')->get()
            ->makeHidden(['account', 'payable', 'user'])->first();
        $due_payment = Payment::myPayments()->due()->groupBy('payable_id')
            ->selectRaw('sum(amount) as total_amount')->get()
            ->makeHidden(['account', 'payable', 'user'])->first();
        $invoice = Invoice::myInvoices()->active()->groupBy('customer_id')
            ->selectRaw('sum(grand_total) as total_amount, sum(total_tax_amount) as total_tax_amount')->get()
            ->makeHidden(['taxes', 'customer'])->first();
        return ['invoice' => $invoice, 'payment' => ['received' => $received_payment, 'due' => $due_payment]];
    }

    public function lineChart(Request $request)
    {

        $year = $request->year ?: date('Y');
        $month = $request->month ?: date('m');
        $date = Carbon::create($year, $month);

        $invoices = chartData('App\Invoice', 'Y-m-d', 'd', 'toDays', $year, $month);
        $purchases = chartData('App\Purchase', 'Y-m-d', 'd', 'toDays', $year, $month);
        Carbon::setLocale('he');
        for ($i = 1; $i <= $date->endOfMonth()->day; $i++) {
            $labels[] = Carbon::create($year, $month, $i)->translatedFormat('jS');
        }


        $config = [
            'title' => $date->translatedFormat('F Y'),
            'datasets' => [
                'Invoices' => $invoices,
                'Purchases' => $purchases,

            ],
            'labels' => $labels,
            'options' => ['responsive' => false, 'maintainAspectRatio' => false, 'legend' => ['display' => true, 'position' => 'top']],
        ];
        $chart = new LineChart($config);

        return $chart->get();
    }

    public function pieChart(Request $request)
    {
        $year = $request->year ?: date('Y');
        $month = $request->month ?: date('m');
        $date = Carbon::create($year, $month);
        Carbon::setLocale('he');
        $config = [
            'title' => $date->translatedFormat('F Y'),
            'datasets' => [
                Invoice::mine()->monthly($year, $month)->count(),
                Purchase::mine()->monthly($year, $month)->count(),
                Income::mine()->monthly($year, $month)->count(),
                Expense::mine()->monthly($year, $month)->count(),
            ],
            'labels' => ['Invoices', 'Purchases', 'Incomes', 'Expenses'],
            'options' => ['responsive' => false, 'maintainAspectRatio' => true, 'legend' => ['display' => true, 'position' => 'top']],
        ];
        $chart = new PieChart($config);

        return $chart->get();
    }

    public function vendor()
    {
        $received_payment = Payment::myPayments()->received()->groupBy('payable_id')
            ->selectRaw('sum(amount) as total_amount')->get()
            ->makeHidden(['account', 'payable', 'user'])->first();
        $due_payment = Payment::myPayments()->due()->groupBy('payable_id')
            ->selectRaw('sum(amount) as total_amount')->get()
            ->makeHidden(['account', 'payable', 'user'])->first();
        $purchase = Purchase::myPurchases()->active()->groupBy('vendor_id')
            ->selectRaw('sum(grand_total) as total_amount, sum(total_tax_amount) as total_tax_amount')->get()
            ->makeHidden(['taxes', 'vendor'])->first();
        return ['purchase' => $purchase, 'payment' => ['received' => $received_payment, 'due' => $due_payment]];
    }

    public function projectsByIds($projectsIds)
    {
        return(new Task)->sumTasksTimeByProjectsId($projectsIds);
    }

    private function projectsByDate()
    {
        return(new Task)->getTasksByProjectsStartDate();
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getProjectsIds(Request $request): array
    {
        return array_map(function ($project) {
            $proj = json_decode($project, true);
            return $proj['id'];

        }, $request->projects);
    }

    /**
     * @param array $projectsData
     * @return array[]
     */
    private function groupProjectsData(array $projectsData): array
    {
        $groupProjectsData = [];
        $categoriesNames = [];

        foreach ($projectsData as $value) {

            $categoriesNames[$value->category_id] = $value->name;
            $groupProjects = $value->project_id;

            if (!isset($groupProjectsData[$groupProjects])) {
                $groupProjectsData[$groupProjects] = [];
            }

            $groupProjectsData[$groupProjects][] = $value;
        }
        ksort($categoriesNames);
        return array($groupProjectsData, $categoriesNames );
    }

    /**
     * @param array $categoriesNames
     * @param array $groupProjectsData
     * @return array
     */
    private function createFullProjectTimeData(array $categoriesNames, array $groupProjectsData): array
    {

        foreach (array_keys($categoriesNames) as $categoryId) {
            foreach ($groupProjectsData as $project) {
                foreach ($project as $category) {
                    $key = array_search($project, $groupProjectsData);
                    if (!in_array($categoryId, array_column($project, 'category_id'))) {

                        $projectData = json_encode([
                            'category_id' => $categoryId,
                            'project_id' => $category->project_id,
                            'actual_time' => '0',
                            'project_name' => $category->project_name,
                            'name' => $category->name
                        ]);
                        $groupProjectsData[$key][] = json_decode($projectData);

                        usort($groupProjectsData[$key], fn($a, $b) => strcmp($a->category_id, $b->category_id));
                        break;
                    }

                }
            }
        }

        return $groupProjectsData;
    }

    /**
     * @param array $groupProjectsData
     * @return array
     */
    private function formatProjectsData(array $groupProjectsData): array
    {
        $formatProjectsData = [];
        foreach ($groupProjectsData as $projectData) {
            foreach ($projectData as $timeData) {

                $actualTime = !$timeData->actual_time ? 0 : $timeData->actual_time;

                $formatProjectsData[$timeData->project_name][] = $actualTime;;
            }

        }

        return $formatProjectsData;
    }

    /**
     * @param array $groupProjectsData
     * @param array $categoriesKeyValue
     * @param array $categoriesNames
     * @param array $formatProjectsData
     * @return array[]
     */
    private function setLabels(array $groupProjectsData, array $categoriesKeyValue, array $categoriesNames, array $formatProjectsData): array
    {
        if (count($groupProjectsData) > 1) {
            ksort($categoriesKeyValue);
        } else {
            $categoriesKeyValue = array_values($categoriesKeyValue);
        }
        $hasNullOnManyProjects = in_array(null, $categoriesNames);
        $hasNullOnSingleProjects = in_array(null, $categoriesKeyValue);

        if ($hasNullOnManyProjects) {

            $index = array_search(null, $categoriesNames);
            unset($categoriesNames[$index]);
            $categoriesNames[0] = 'ללא קטגוריה';

        }
        if ($hasNullOnSingleProjects) {

            $index = array_search(null, $categoriesKeyValue);
            $categoriesKeyValue[$index] = 'ללא קטגוריה';

        }

        return count($formatProjectsData) > 1 ? $categoriesNames : $categoriesKeyValue;
    }

    private function aggregateProjectData(array $projectsData)
    {

        $projectsActualTime =json_encode((new Task())->sumProjectActualTime(array_unique(array_column($projectsData, 'project_id'))));
        $projectsPrice = Project::whereIn('id', array_unique(array_column($projectsData, 'project_id')))
            ->select('id as project_id','price', 'name')
            ->get()->toArray();


        $projectsData = [];
        foreach(json_decode($projectsActualTime,true) as $projectActualTime) {

           foreach ($projectsPrice as $projectPrice) {
               if ($projectActualTime['project_id'] === $projectPrice['project_id'] ) {
                   $id = $projectPrice['project_id'];
                   //dd($projectActualTime['actual_time']);
                   $projectsData[$id] = [
                       'price' => $projectPrice['price'],
                       'actual_time' => $projectActualTime['actual_time'],
                       'name' => $projectPrice['name']
                   ];
               }
           }

        }

    return $projectsData;
    }

    private function createCategoriesDatasets(array $categoriesTime, array $categoriesName)
    {
        $categories = [];
        foreach ($categoriesTime as $categoryTime) {
                foreach ($categoriesName as $key => $categoryName) {
                    if ($categoryName === $categoryTime->name) {
                        $name = !$categoryName ? 'ללא קטגוריה' : $categoryName;
                        $actualTime = !$categoryTime->actual_time ? "0" : $categoryTime->actual_time;
                        $categories[$name][] = $actualTime;
                    }
                }
        }

        return $categories;
    }
}
