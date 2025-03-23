<?php
session_start();
include "../koneksi.php";

if (!isset ($_SESSION["login"])) {
    header("Location: ../index.php");
    exit;
}
if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
} else {
    $username = "";
}
?>

<!doctype html>

<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">

<head>
    <meta charset="UTF-8">
    <script type="text/javascript" src="./Chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Grafik Pelanggaran Harian</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link rel="apple-touch-icon" href="apple-icon.png">
    <link rel="shortcut icon" href="favicon.ico">

    <link rel="stylesheet" href="vendors/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="vendors/selectFX/css/cs-skin-elastic.css">
    <link rel="stylesheet" href="style3.css">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
    <style type="text/css">
    .chartCard {
            width: 100%;
            height: calc(100vh - 40px);
            display: flex;
            justify-content: center;
            align-content : center;
        }
    	.chartBox{
            width: 80%;
            height: 80%;
            border-radius:10px;
            border: solid 3px #5D1D86;
            background: white;
        }
        @media (max-width: 900px){
            .chartCard{
            width: 100%;
            display: flex;
            justify-content: center;
            align-content: center;
            padding-left: 15px;
            }
        }
	</style>

</head>

<body>
    <!-- Left Panel -->

    <aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default">

            <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i></button>
                <a class="navbar-brand" href="./"><img src="images/pnm.png" alt="Logo"></a>
                <a class="navbar-brand hidden" href="./"><img src="images/pnm.png" alt="Logo"></a>
            </div>

            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="dashboard.php"> <i class="menu-icon fa fa-dashboard"></i>Dashboard </a>
                    </li>

                    <li>
                        <a href="tables-data.php"> <i class="menu-icon fa fa-book"></i>Data Pelanggaran </a>
                    </li>
                    
                    <li>
                        <a href="chartkebisingan.php"> <i class="menu-icon fa fa-bar-chart-o"></i>Grafik Kebisingan </a>
                    </li>
                    
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-bar-chart-o"></i>Grafik Pelanggaran</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-calendar"></i><a href="chartday.php">Harian</a></li>
                            <li><i class="fa fa-signal"></i><a href="chartjenis.php">Jenis</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="rekap.php"> <i class="menu-icon fa fa-calendar-o"></i>Grafik Bulanan </a>
                    </li>
                    <li class="logout">
                  		<a onclick="confirmLogout()" href="#"> <i class="menu-icon fa fa-sign-out"></i>Logout </a>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>
    </aside><!-- /#left-panel -->

    <!-- Left Panel -->

    <!-- Right Panel -->

    <div id="right-panel" class="right-panel">

        <!-- Header-->
        <header id="header" class="header">
            <div class="header-menu">
                <div class="col-sm-7">      
                    <a id="menuToggle" class="menutoggle pull-left"><i class="fa fa fa-tasks"></i></a>
                    <div class="header-left">
                        <div class="dropdown for-notification"> <h4 class='text-white'>Monitoring APD GRINDER</h4></div>
                        <div class="dropdown for-message"></div>
                    </div>
                </div>

                <div class="col-sm-5">
                    <div class="user-area dropdown float-right">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="user-avatar rounded-circle" src="images/admin.jpg" alt="User Avatar">
                        </a>
                        <div class="user-menu dropdown-menu">
                            <a class="nav-link" href="../logout.php"><i class="fa fa-power-off"></i> Logout</a>
                        </div>
                        <?php
                        echo "<span>Hallo, " . htmlspecialchars($_SESSION["username"]) . "</span>";
                        ?>
                    </div>
                </div>
            </div>
        </header><!-- /header -->
        <!-- Header-->

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>Grafik Harian</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="dashboard.php">Dashboard</a></li>
                            <li><a href="chartday.php">Grafik</a></li>
                            <li class="active">Harian</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div class="content mt-3">
            <div class="animated fadeIn">
                    <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="startDate">Tanggal Mulai:</label>
                        <input type="date" id="startDate" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="endDate">Tanggal Akhir:</label>
                        <input type="date" id="endDate" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <button id="filterButton" class="btn btn-primary" style="margin-top: 30px;">Filter</button>
                    </div>
                    <button onclick="downloadPDF()" class ="mt-3 mb-3 ml-3 p-2 btn-warning">Cetak Grafik Harian</button>
                    <button onclick="exportToExcel()" class="mt-3 mb-3 ml-3 p-2 btn-primary">Export to Excel</button>
                </div>
                <div class="row"> 
                    <div class="chartCard">
                        <div class="chartBox">
                            <canvas id="myChart"></canvas>
                        </div>
                    </div>
                    
                   <?php
                        $labels = [];
                        $counts = [];
                        
                        // Generate dates for the last 30 days
                        $today = new DateTime();
                        $startDate = (clone $today)->modify('-30 days');
                        $interval = new DateInterval('P1D'); // 1 day interval
                        $period = new DatePeriod($startDate, $interval, $today);
                        
                        foreach ($period as $date) {
                            $formattedDate = $date->format('Y-m-d');
                            $labels[] = $formattedDate;
                            $counts[$formattedDate] = 0; // Initialize with 0
                        
                            // Fetch data for this date
                            $sql = "SELECT COUNT(*) AS count FROM data_pelanggaran WHERE tanggal = '$formattedDate'";
                            $result = $conn->query($sql);
                            $row = $result->fetch_assoc();
                            if ($row) {
                                $counts[$formattedDate] = $row['count'];
                            }
                        }
                        
                        // Convert associative array to indexed array
                        $countsArray = array_values($counts);
                        ?>


                    <br/>
                    <br/>
                    

                    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
                    <script>
                    Chart.defaults.font.size = 14;
                    Chart.defaults.font.weight = 400;
                    
                    const ctx = document.getElementById('myChart').getContext('2d');
                    const labels = <?php echo json_encode($labels); ?>;
                    const counts = <?php echo json_encode($countsArray); ?>;
                    
                    const myChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: '',
                                data: counts,
                                fill: true,
                                backgroundColor: 'rgba(161, 162, 255, 0.1)',
                                borderColor: 'rgba(29, 32, 196, 1)',
                                pointBorderWidth: 6,
                                borderWidth: 4
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        title: context => {
                                            console.log(context);
                                        }
                                    }
                                },
                                title: {
                                    display: true,
                                    text: ['GRAFIK HARIAN PELANGGARAN', 'APD GRINDER'],
                                    font: { size: 18 },
                                    padding: 40
                                },
                                legend: {
                                    display: false,
                                }
                            },
                            scales: {
                                y: {
                                    title: {
                                        padding: 20,
                                        font: {
                                            size: 16
                                        },
                                        display: true,
                                        text: 'Jumlah'
                                    },
                                    grid: {
                                        display: false
                                    },
                                    beginAtZero: true
                                },
                                x: {
                                    type: 'time',
                                    time: {
                                        unit: 'day'
                                    },
                                    title: {
                                        padding: 20,
                                        font: {
                                            size: 16
                                        },
                                        display: true,
                                        text: 'Tanggal'
                                    },
                                    grid: {
                                        display: true
                                    }
                                }
                            }
                        }
                    });
                    
                    document.getElementById('filterButton').addEventListener('click', function() {
                        const startDate = new Date(document.getElementById('startDate').value);
                        const endDate = new Date(document.getElementById('endDate').value);
                        
                        // Filter data
                        const filteredLabels = [];
                        const filteredCounts = [];
                        
                        for (let i = 0; i < labels.length; i++) {
                            const date = new Date(labels[i]);
                            if (date >= startDate && date <= endDate) {
                                filteredLabels.push(labels[i]);
                                filteredCounts.push(counts[i]);
                            }
                        }
                    
                        // Update chart
                        myChart.data.labels = filteredLabels;
                        myChart.data.datasets[0].data = filteredCounts;
                        myChart.update();
                    });
                    
                    async function downloadPDF() {
                        const { jsPDF } = window.jspdf;
                        const pdf = new jsPDF();
                        const canvas = document.getElementById('myChart');
                        const imgData = canvas.toDataURL('image/png');
                        pdf.addImage(imgData, 'PNG', 10, 10, 180, 160);
                        pdf.save('Grafik Harian.pdf');
                    }
                    
                    function exportToExcel() {
                        const data = labels.map((label, index) => ({ Tanggal: label, Jumlah: counts[index] }));
                    
                        const worksheet = XLSX.utils.json_to_sheet(data);
                        const workbook = XLSX.utils.book_new();
                    
                        // Set column widths
                        worksheet['!cols'] = [
                            { width: 12 }, // Width for 'Tanggal' column
                            { width: 12 }  // Width for 'Jumlah' column
                        ];
                    
                        XLSX.utils.book_append_sheet(workbook, worksheet, "Grafik Harian");
                    
                        XLSX.writeFile(workbook, 'Grafik_Harian.xlsx');
                    }
                    </script>

                </div> 
            </div><!-- .animated -->
        </div><!-- .content -->
    </div><!-- /#right-panel -->

    <!-- Right Panel -->

    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <script src="vendors/popper.js/dist/umd/popper.min.js"></script>
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
    <!--  Chart js -->
    <script src="vendors/chart.js/dist/Chart.bundle.min.js"></script>
    <script src="assets/js/init-scripts/chart-js/chartjs-init.js"></script>

</body>

</html>
