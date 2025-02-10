@extends('layouts.index')

@section('content')
<div class="container py-8 bg-white">
    <h1 class="text-2xl font-semibold mb-6">Dashboard</h1>

    <!-- Date Filter -->
    <div class="mb-6">
        <form action="{{ route('dashboard') }}" method="GET" class="flex items-center space-x-4">
            <div>
                {{-- <label for="date" class="text-sm font-bold mr-2">Filter by Date:</label> --}}
                <input type="date" name="date" id="date" value="{{ request('date') }}" class="bg-white border border-gray-300 rounded px-4 py-2">
                <button type="submit" class="btn btn-primary ml-4 bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Left Column: Income Bar Chart -->
            <div class="bg-white shadow-md rounded-lg p-4">
                <h5 class="text-lg font-semibold mb-4">Income Summary by Month</h5>
                <canvas id="income-chart" class="w-full h-full"></canvas>
            </div>
        </div> 
        <div class="col-md-4">
            <div class="col-md-12">
                <div class="bg-white shadow-md rounded-lg p-4">
                    <h5 class="text-lg font-semibold mb-4">Most Sold Items</h5>
                    <canvas id="most-sold-chart" class="w-full h-64"></canvas>
                </div>
            </div>
            <div class="col-md-12">
                <div class="bg-white shadow-md rounded-lg p-4">
                    <h5 class="text-lg font-semibold mb-4">Order Status Summary</h5>
                    <canvas id="order-status-chart" class="w-full h-64"></canvas>
                </div>
            </div>
        </div>
    </div>


</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

    // Define all 12 months
    const allMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    
    // Convert income data to a keyed object (Month -> Income)
    const incomeData = @json($incomeData);
    const incomeByMonth = {};

    // Fill with available data
    incomeData.forEach(item => {
        incomeByMonth[item.month] = item.income;
    });

    // Ensure all 12 months are included
    const incomeValues = allMonths.map((month, index) => incomeByMonth[index + 1] || 0);

    // Income Chart (Bar graph)
    var ctxIncome = document.getElementById('income-chart').getContext('2d');
    var incomeChart = new Chart(ctxIncome, {
        type: 'bar',
        data: {
            labels: allMonths, // Always display all 12 months
            datasets: [{
                label: 'Income ($)',
                data: incomeValues, // Fallback to 0 for missing months
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Month'
                    }
                },
                y: {
                    beginAtZero: true, // Ensure graph starts from 0
                    title: {
                        display: true,
                        text: 'Income ($)'
                    }
                }
            }
        }
    });

    // Order Status Chart (Pie Chart)
    var ctxOrderStatus = document.getElementById('order-status-chart').getContext('2d');
    var orderStatusChart = new Chart(ctxOrderStatus, {
        type: 'pie',
        data: {
            labels: @json($orderStatusSummary->pluck('status')), // Order status labels
            datasets: [{
                label: 'Order Status',
                data: @json($orderStatusSummary->pluck('count')), // Status count
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#F7464A'],
                borderWidth: 1
            }]
        }
    });

    // Most Sold Items Chart (Pie Chart)
    var ctxMostSold = document.getElementById('most-sold-chart').getContext('2d');
    var mostSoldChart = new Chart(ctxMostSold, {
        type: 'pie',
        data: {
            labels: @json($mostSoldItems->pluck('name')), // Item names
            datasets: [{
                label: 'Most Sold Items',
                data: @json($mostSoldItems->pluck('total_sold')), // Total sold count
                backgroundColor: ['#FF9F40', '#FF6384', '#36A2EB', '#4BC0C0', '#9966FF', '#F7464A'],
                borderWidth: 1
            }]
        }
    });
</script>

@endsection
