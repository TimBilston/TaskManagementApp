
<?php 
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use App\Model\Entity\Task;

  $NotComplted = 0;
  $Completed = 0;
  //$this->disableAutoLayout();
  $query = TableRegistry::getTableLocator()->get('Tasks')->find();
  $query->contain(['Users']);
  $query->contain(['Status']);
  $query->contain(['Clients']);
  // $navData = array(
  //   array('name'=>'Completed', 'value'=>0, 'dueDate'=>'', 'itemStyle'=>array('color'=>'green')),
  //   array('name'=>'Not Completed', 'value'=>0,'dueDate'=>'', 'itemStyle'=>array('color'=>'#b80c3c')),
  // );
  $navData = Array();
  foreach ($query as $task) {
      echo $task['due_data'];
      if($task->status->name=='Completed'){
        $Completed += 1;
        array_push($navData, array('name'=>'Completed', 'value'=>1, 'dueDate'=> $task->due_date));
      }else{
        $NotComplted += 1;
        array_push($navData, array('name'=>'Not Completed', 'value'=>1, 'dueDate'=> $task->due_date));
      }
  }

  $allData = Array();
  $OverDue = 0;
  $allTotal = 0;
  foreach ($query as $task) {
    if($task->status->name=='Over Due'){
      $OverDue += 1;
      array_push($allData, array('name'=>'OverDue', 'value'=>1, 'dueDate'=> $task->due_date));
    }
    $allTotal += 1;
    array_push($allData, array('name'=>'Total', 'value'=>1, 'dueDate'=> $task->due_date));
  }

  $BarAllData = Array();
  $BarInProgress = 0;
  $BarCompleted = 0;
  $BarOverDue = 0;
  $BarAttentionNeeded = 0;
  foreach ($query as $task) {
    if($task->status->name=='Over Due'){
      $BarOverDue += 1;
      array_push($BarAllData, array('name'=>'OverDue', 'value'=>1));
    }else if($task->status->name=='Completed'){
      $BarCompleted += 1;
      array_push($BarAllData, array('name'=>'Completed', 'value'=>1));
    }else if($task->status->name=='Attention Needed'){
      $BarAttentionNeeded += 1;
      array_push($BarAllData, array('name'=>'AttentionNeeded', 'value'=>1));
    }else if($task->status->name=='In Progress'){
      $BarInProgress += 1;
      array_push($BarAllData, array('name'=>'InProgress', 'value'=>1));
    }
  }
  ?>
<!DOCTYPE html>
<html></html>
<title> KPI </title>

<head>
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= $this->Html->meta('icon') ?>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
</head>
<link rel="stylesheet" href="webroot/css/kanban.css">
<link rel="stylesheet" href="webroot/css/tasks.css">
<link rel="stylesheet" href="webroot/css/custom.css">
<link rel="stylesheet" href="webroot/css/buttons.css">
<style>
  #total{
    width:100%;
    height:600px;
    border-bottom:1px solid #d9d9d9;
  }
  #overdue{
    width:100%;
    height:600px;
    margin-top:50px;
    border-bottom:1px solid #d9d9d9;
  }
  #totalBar{
    width:100%;
    height:600px;
    margin-top:50px;
  }
</style>
<body>
  <div id="total"></div>
  <div id="overdue"></div>
  <div id="totalBar"></div>
  <script>
  var navData = <?php echo json_encode($navData) ?>; //get data 
  function process(arr) {
    const cache = [];
    for (const t of arr) { 
        if (cache.find(c => c.name === t.name && c.dueDate === t.dueDate)) {   //delete repeat things
          cache.find(c => c.name === t.name && c.dueDate === t.dueDate).value += 1
        }else{
          cache.push(t);
        }
    }
    return cache;
  }
  var formatData = process(navData)   
  formatData.forEach(item=>{    //add default value 
    if(item.name == "Completed"){
      item['Completed'] = item.value
    }else{
      item['NotCompleted'] = item.value
    }
  })
    var NewformatData = newProcess(formatData)  //according to date delete repeat thing again
    function newProcess(arr) {
      const cache = [];
      for (const t of arr) {
          if (cache.find(c => c.dueDate === t.dueDate)) {
            if(cache.find(c => c.dueDate === t.dueDate).name == 'Completed'){
              cache.find(c => c.dueDate === t.dueDate)['NotCompleted']  = t.NotCompleted
            }else{
              cache.find(c => c.dueDate === t.dueDate)['Completed']  = t.Completed
            }
          }else{
            cache.push(t);
          }
      }
      return cache;
    }
    var dom = document.getElementById("total");
    var myChart = echarts.init(dom);
    var option;

    option = {
        title: {
            text: 'total tasks',
        },
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data: ['Completed', 'Not Completed']
        },
        calculable: true,
        xAxis: [
            {
                type: 'category',
                data: NewformatData.map(i=>i.dueDate)
            }
        ],
        yAxis: [
            {
                type: 'value',
                minInterval: 1
            }
        ],
        series: [
            {
                name: 'Completed',
                type: 'bar',
                data: NewformatData.map(i=>i.Completed || 0),
                barMinHeight: 4,
            },
            {
                name: 'Not Completed',
                type: 'bar',
                data: NewformatData.map(i=>i.NotCompleted || 0),
                barMinHeight: 4,
            }
        ]
    };

    if (option && typeof option === 'object') {
        myChart.setOption(option);
    }
    var allData = <?php echo json_encode($allData) ?>; //get data 
    function allProcess(arr) {
      const cache = [];
      for (const t of arr) { 
          if (cache.find(c => c.name === t.name && c.dueDate === t.dueDate)) {   //delete repeat things
            cache.find(c => c.name === t.name && c.dueDate === t.dueDate).value += 1
          }else{
            cache.push(t);
          }
      }
      return cache;
    }
    var formatAllData = allProcess(allData) 
    formatAllData.forEach(item=>{    //add default value 
      if(item.name == "OverDue"){
        item['OverDue'] = item.value
      }else{
        item['Total'] = item.value
      }
    })
    var NewformatAllData = newAllProcess(formatAllData)  //according to date delete repeat thing again
    function newAllProcess(arr) {
      const cache = [];
      for (const t of arr) {
          if (cache.find(c => c.dueDate === t.dueDate)) {
            if(cache.find(c => c.dueDate === t.dueDate).name == 'OverDue'){
              cache.find(c => c.dueDate === t.dueDate)['Total']  = t.Total
            }else{
              cache.find(c => c.dueDate === t.dueDate)['OverDue']  = t.OverDue
            }
          }else{
            cache.push(t);
          }
      }
      return cache;
    }
    var overdue = document.getElementById("overdue");
    var overdueMyChart = echarts.init(overdue);
    var overDueOption;
    overDueOption = {
      title: {
          text: 'overdue tasks',
      },
      tooltip: {
          trigger: 'axis'
      },
      legend: {
          data: ['Total', 'OverDue']
      },
      calculable: true,
      xAxis: [
          {
              type: 'category',
              data: NewformatAllData.map(i=>i.dueDate)
          }
      ],
      yAxis: [
          {
              type: 'value',
              minInterval: 1,
          }
      ],
      series: [
          {
              name: 'OverDue',
              type: 'bar',
              data: NewformatAllData.map(i=>i.OverDue || 0),
              itemStyle:{
                color:'#b80c3c'
              },
              barMinHeight: 4,
          },
          {
              name: 'Total',
              type: 'bar',
              data: NewformatAllData.map(i=>i.Total || 0),
              itemStyle: {
                color:'green'
              },
              barMinHeight: 4,
          }
      ]
    }
    if (overDueOption && typeof overDueOption === 'object') {
      overdueMyChart.setOption(overDueOption);
    }



    var BarAllData = <?php echo json_encode($BarAllData) ?>; //get data 
    console.log(BarAllData)
    function barProcess(arr) {
      const cache = [];
      for (const t of arr) { 
          if (cache.find(c => c.name === t.name)) {   //delete repeat things
            cache.find(c => c.name === t.name).value += 1
          }else{
            cache.push(t);
          }
      }
      return cache;
    }
    var formatBarData = barProcess(BarAllData) 
    
    console.log(formatBarData)
    var totalBar = document.getElementById("totalBar");
    var BarMyChart = echarts.init(totalBar);
    var BarOption;
    BarOption  = {
      title: {
        text: 'All Task Ratio',
      },
      tooltip: {
        trigger: 'item'
      },
      legend: {
        left: 'center'
      },
      series: [
        {
          type: 'pie',
          radius: ['40%', '70%'],
          data: formatBarData,
          itemStyle: {
            borderRadius: 10,
            borderColor: '#fff',
            borderWidth: 2,
            normal:{
              label:{
                position : 'outer',
                formatter : function (params){
                    if(params.percent){
                        return (params.percent - 0) + '%';
                    }else{
                        return '';
                    }
                },
                textStyle: {
                    color: "#333",
                    fontSize:14,
                    fontWeight:'bold'
                }
              },
              labelLine:{
                show:true
              }                     
            }
          },
        }
      ]
    };
    if (BarOption && typeof BarOption === 'object') {
      BarMyChart.setOption(BarOption);
    }
  </script>
</body>