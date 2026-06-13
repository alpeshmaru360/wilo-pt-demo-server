@extends('layouts.admin')
@section('content')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

<style>
  .button {
      font-size:17px;
      margin-top:20px;
      display: block;
      width: 228px;
      height: 49px;
      background: #169e88;
      padding: 10px;
      text-align: center;
      border-radius: 5px;
      color: white;
      font-weight: bold;
      line-height: 25px;
  }
  .button:hover{
    text-decoration:none;
    color:white;
  }
  .text-primary {
    color: #169e88 !important;
  }
  .bg-primary {
    background: #169e88 !important;
  }
  a.text-primary:focus, a.text-primary:hover {
    color: #0baa91 !important;
    text-decoration: none;
  }
  .blink_me {
    animation: blinker 1s linear infinite;
  }

  @keyframes blinker {
    50% {
      opacity: 0;
    }
  }
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">{{ trans('global.dashboard') }}</h1>
            <div class="row w-100 pl-3 d-flex align-items-baseline show-download-log">
              <select class="mr-3 d-flex font-weight-bold border-dark  mt-3 text-primary year_selection" style="border-color: #169e88 !important;">
                  <option class="">Select a year</option>
                @foreach($years as $val)
                  <option value="{{$val}}">{{$val}}</option>
                @endforeach
              </select>
              <select class="mr-3 d-flex font-weight-bold border-dark mt-3 text-primary month_selection" style="border-color: #169e88 !important;">
                <option value="">Select a month</option>
                <option value="01">January</option>
                <option value="02">February</option>
                <option value="03">March</option>
                <option value="04">April</option>
                <option value="05">May</option>
                <option value="06">June</option>
                <option value="07">July</option>
                <option value="08">August</option>
                <option value="09">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>
              <a href="javascript:;" class="button download-quotation-log">Download Quotation Log</a>
              <div class="download-quotation-process ml-2 d-flex align-items-center blink_me" style="display: none !important;">
                <i class="fa fa-spinner fa-spin text-primary"></i><p class="p-0 m-0 ml-1 text-primary ">Initialize Processes</p>
              </div>
            </div>
        </div><!-- /.col -->
        <div class="col-sm-6">
            @include('layouts.breadcrumbs')
            <div class="row w-100 pl-3 d-flex align-items-baseline show-download-log">
                <a href="{{ url('admin/all-quotation-list') }}" class="d-flex font-weight-bold justify-content-end mt-3 pt-4 text-primary w-100 all-qutaion-log">All Quotation Log</a>
            </div>
        </div><!-- /.col -->
        </div><!-- /.row -->
        <div class="row m-0 p-0 w-100 progress-show" style="display: none;">
          <div class="progress w-100">
            <div class="progress-bar" role="progressbar" style="width: 0%;background: #169e88;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
          </div>
        </div>
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<!-- Main content -->
<section class="content">
@include('layouts.message')

<div class="container-fluid">
  <div class="row">
  </div>
</div>

<div class = container-fluid>
  <div class = "row">
    <div class="col-sm-6">
    <h3>Pie Chart - Country wise total Quotation</h3>
        <canvas id="myChart"></canvas>
    </div>
    <div class="col-sm-6">
    <h3>Pie Chart - Country wise total Quotation value</h3>
        <canvas id="myChart1"></canvas>
    </div>
  </div>
</div>
<br><br>

<div class = container-fluid>
  <div class = "row">
    <div class="col-sm-6">
    </div>
    <div class="col-sm-6">
    </div>
  </div>
</div>

</section>
@endsection

@section('scripts')    
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

  <script type="text/javascript">
      var filename = '';
      var selected_year = '';
      var selected_month = '';

      $(document).on('change', '.year_selection', function() {
          selected_year = $(this).val();
      });

      $(document).on('change', '.month_selection', function() {
          selected_month = $(this).val();
      });

      $(document).on('click', '.download-quotation-log', function() {
          if (!selected_year || selected_year === '' || selected_year === 'Select a year') {
              alert("Please select a Year");
              return;
          }
          if (!selected_month || selected_month === '' || selected_month === 'Select a month') {
              alert("Please select a Month");
              return;
          }
          $('.download-quotation-process').show();
          $('.progress-show').show();
          ajaxCall(filename, selected_year, selected_month);
      });

      function ajaxCall(filename, selected_year, selected_month) {
          $.ajax({
              url: "{{ url('admin/dashboard/new-export-quotation') }}",
              method: 'POST',
              data: {
                  _token: "{{csrf_token()}}",
                  filename: filename,
                  selected_year: selected_year,
                  selected_month: selected_month
              },
              // Explicitly avoid dataType to prevent JSON parsing
              beforeSend: function() {
                  $(".download-quotation-log").prop('disabled', true).css('cursor', 'no-drop');
                  $('.download-quotation-process').find('i').removeAttr('class').addClass('fa fa-spinner fa-spin text-primary');
                  $('.download-quotation-process').find('p').removeAttr('class').addClass('p-0 m-0 ml-1 text-primary').text('Generating file...');
                  $('.progress-show').find('.progress').find('.progress-bar').css('width', '100%').text('Processing...');
              },
              success: function(data, textStatus, xhr) {
                  // Only handle JSON responses
                  if (xhr.getResponseHeader('Content-Type').includes('application/json')) {
                      console.log('JSON Success response:', data);
                      if (data['success'] === 'no-data') {
                          alert('No quotation data available for the selected Month & Year.');
                          $('.download-quotation-process').hide();
                          $('.progress-show').hide();
                          $(".download-quotation-log").prop('disabled', false).css('cursor', 'pointer');
                          location.reload();
                      } else {
                          console.error('Unexpected JSON response:', data);
                          alert('Unexpected JSON response from server. Please try again.');
                          $('.download-quotation-process').hide();
                          $('.progress-show').hide();
                          $(".download-quotation-log").prop('disabled', false).css('cursor', 'pointer');
                      }
                  } else {
                      console.log('Non-JSON response received in success, Content-Type:', xhr.getResponseHeader('Content-Type'));
                      // Let complete callback handle text/csv
                  }
              },
              error: function(xhr, status, error) {
                  console.error('AJAX Error:', status, error, xhr);
                  let errorMsg = 'Server error';
                  if (xhr.status === 200 && xhr.getResponseHeader('Content-Type').includes('text/csv')) {
                      console.log('CSV response in error callback, handling in complete');
                      return; // Let complete callback handle
                  }
                  try {
                      const jsonResponse = JSON.parse(xhr.responseText);
                      if (jsonResponse.success === 'no-data') {
                          alert('No quotation data available for the selected Month & Year.');
                          location.reload();
                          return;
                      }
                      errorMsg = jsonResponse.message || error;
                  } catch (e) {
                      errorMsg = xhr.statusText || error;
                  }
                  console.error('Error details:', errorMsg, 'Response:', xhr.responseText);
                  $('.download-quotation-process').find('i').removeAttr('class').addClass('fa fa-exclamation-triangle text-danger');
                  $('.download-quotation-process').find('p').removeAttr('class').addClass('p-0 m-0 ml-1 text-danger').text('Process Failed: ' + errorMsg);
                  $('.download-quotation-process').hide();
                  $('.progress-show').hide();
                  $(".download-quotation-log").prop('disabled', false).css('cursor', 'pointer');
              },
              xhrFields: {
                  responseType: 'blob' // Ensure response is treated as Blob for CSV
              },
              complete: function(xhr) {
                  if (xhr.status === 200 && xhr.getResponseHeader('Content-Type').includes('text/csv')) {
                      console.log('Handling CSV Blob response, Content-Type:', xhr.getResponseHeader('Content-Type'), 'Response:', xhr.response);
                      const blob = xhr.response;
                      // Check if blob is valid
                      if (!blob || !(blob instanceof Blob)) {
                          console.error('Invalid or undefined Blob response:', blob);
                          alert('Failed to download CSV: No valid data received from server.');
                          $('.download-quotation-process').hide();
                          $('.progress-show').hide();
                          $(".download-quotation-log").prop('disabled', false).css('cursor', 'pointer');
                          return;
                      }
                      // Log Blob details
                      console.log('Blob details - size:', blob.size, 'type:', blob.type);
                      // Debug Blob content (sample first 100 bytes)
                      const reader = new FileReader();
                      reader.onload = function(e) {
                          console.log('Blob content sample (first 100 bytes):', e.target.result.slice(0, 100));
                      };
                      reader.onerror = function(e) {
                          console.error('Failed to read Blob:', e);
                      };
                      reader.readAsText(blob);
                      // Create download link
                      const disposition = xhr.getResponseHeader('Content-Disposition');
                      let downloadFilename = filename || 'quotation_' + selected_year + '_' + selected_month + '_' + Date.now() + '.csv';
                      if (disposition && disposition.indexOf('attachment') !== -1) {
                          const matches = /filename="([^"]*)"/.exec(disposition);
                          if (matches != null && matches[1]) {
                              downloadFilename = matches[1];
                          }
                      }
                      try {
                          const link = document.createElement('a');
                          link.href = window.URL.createObjectURL(blob);
                          link.download = downloadFilename;
                          document.body.appendChild(link);
                          link.click();
                          document.body.removeChild(link);
                          window.URL.revokeObjectURL(link.href); // Clean up
                      } catch (e) {
                          console.error('Failed to create download URL:', e);
                          alert('Failed to download CSV: ' + e.message);
                      }
                      $('.download-quotation-process').hide();
                      $('.progress-show').hide();
                      $(".download-quotation-log").prop('disabled', false).css('cursor', 'pointer');
                  } else {
                      console.log('Complete callback: Non-CSV response, status:', xhr.status, 'Content-Type:', xhr.getResponseHeader('Content-Type'), 'Response:', xhr.response);
                  }
              }
          });
      }
  </script>

  <script type="text/javascript">
    var labelArray = [];
    var dataArray =  [];
    var data = `<?php echo $data3;?>`;
    var cData = jQuery.parseJSON(data);

    var lable =   $(cData).each(function(i,val){
        $.each(val,function(k,v){
          labelArray.push(k); 
          dataArray.push(v);
        });
    });

    var ctx = document.getElementById("myChart").getContext('2d');

    var lableDataArray = labelArray.map((e, i) => e + '\xa0\xa0(' + dataArray[i] +')');

    //console.log(lableDataArray);
    
    var myChart = new Chart(ctx,{
      type: 'pie',
      radius: '80%',
        center: ['10%', '57.5%'],


    data: {
      // labels: ["C1", "C2", "C3", "C4", "C5", "C6", "C7","C8","C9","C10","C11","C12","C13","C14","C15","C16","C17","C18","C19","C20"],
      // lables:dataArray,
      labels:lableDataArray,
      datasets: [{
        backgroundColor: [
          "#2ecc71",
          "#3498db",
          "#1B4F72",
          "#9b59b6",
          "#784212",
          "#e74c3c",
          "#34495e",
          "orange",
          "white",
          "black",
          "cyan",
          "blue",
          "Blue-gray",
          "Blue-violet",
          "violet",
          "Brown",
          "Cream",
          "green",
          "gold",
          "pink"
      ],
      //data: [12, 19, 25, 17, 28, 24, 7,10,11,12,13,14,15,16,17,18,19,20,25,30]
      data:dataArray
      }]
    }

    });
  </script>

  <script type="text/javascript">
      var labelArray = [];
      var dataArray =  [];
      var data = `<?php echo $array_merge;?>`;
      var cData = jQuery.parseJSON(data);

      var lable = $(cData).each(function(i,val){
          $.each(val,function(k,v){
            labelArray.push(k); 
            dataArray.push(v);
          });
      });

      var ctx = document.getElementById("myChart1").getContext('2d');

      var lableDataArray = labelArray.map((e, i) => e + '\xa0\xa0(' + dataArray[i] +' $)');

      var myChart = new Chart(ctx,{
        type: 'pie',
        radius: '80%',
          center: ['10%', '57.5%'],
      data: {
        // labels: ["C1", "C2", "C3", "C4", "C5", "C6", "C7","C8","C9","C10","C11","C12","C13","C14","C15","C16","C17","C18","C19","C20"],
        // lables:dataArray,
        labels:lableDataArray,
        datasets: [{
          backgroundColor: [
            "#2ecc71",
            "#3498db",
            "#1B4F72",
            "#9b59b6",
            "#784212",
            "#e74c3c",
            "#34495e",
            "orange",
            "white",
            "black",
            "cyan",
            "blue",
            "Blue-gray",
            "Blue-violet",
            "violet",
            "Brown",
            "Cream",
            "green",
            "gold",
            "pink"
        ],
        //data: [12, 19, 25, 17, 28, 24, 7,10,11,12,13,14,15,16,17,18,19,20,25,30]
        data:dataArray
        }]
      }
      });
  </script>

  <script type="text/javascript">
      var labelArray = [];
      var dataArray =  [];
      var data = `<?php echo $array_merge;?>`;
      var cData = jQuery.parseJSON(data);

      var lable =   $(cData).each(function(i,val){
          $.each(val,function(k,v){
            labelArray.push(k); 
            dataArray.push(v);
          });
      });

      var ctx = document.getElementById("myChart2").getContext('2d');

      var lableDataArray = labelArray.map((e, i) => e + '\xa0\xa0(' + dataArray[i] +')');

      //console.log(lableDataArray);
      
      var myChart = new Chart(ctx,{
        type: 'pie',
        radius: '80%',
          center: ['10%', '57.5%'],
      data: {
        // labels: ["C1", "C2", "C3", "C4", "C5", "C6", "C7","C8","C9","C10","C11","C12","C13","C14","C15","C16","C17","C18","C19","C20"],
        // lables:dataArray,
        labels:lableDataArray,
        datasets: [{
          backgroundColor: [
            "#2ecc71",
            "#3498db",
            "#1B4F72",
            "#9b59b6",
            "#784212",
            "#e74c3c",
            "#34495e",
            "orange",
            "white",
            "black",
            "cyan",
            "blue",
            "Blue-gray",
            "Blue-violet",
            "violet",
            "Brown",
            "Cream",
            "green",
            "gold",
            "pink"
        ],
        //data: [12, 19, 25, 17, 28, 24, 7,10,11,12,13,14,15,16,17,18,19,20,25,30]
        data:dataArray
        }]
      }
      });
  </script>

  <script type="text/javascript">
      var labelArray = [];
      var dataArray =  [];
      var data = `<?php echo $array_merge;?>`;
      var cData = jQuery.parseJSON(data);

      var lable =   $(cData).each(function(i,val){
          $.each(val,function(k,v){
            labelArray.push(k); 
            dataArray.push(v);
          });
      });

      var ctx = document.getElementById("myChart3").getContext('2d');

      var lableDataArray = labelArray.map((e, i) => e + '\xa0\xa0(' + dataArray[i] +')');

      //console.log(lableDataArray);
      
      var myChart = new Chart(ctx,{
        type: 'pie',
        radius: '80%',
          center: ['10%', '57.5%'],
      data: {
        // labels: ["C1", "C2", "C3", "C4", "C5", "C6", "C7","C8","C9","C10","C11","C12","C13","C14","C15","C16","C17","C18","C19","C20"],
        // lables:dataArray,
        labels:lableDataArray,
        datasets: [{
          backgroundColor: [
            "#2ecc71",
            "#3498db",
            "#1B4F72",
            "#9b59b6",
            "#784212",
            "#e74c3c",
            "#34495e",
            "orange",
            "white",
            "black",
            "cyan",
            "blue",
            "Blue-gray",
            "Blue-violet",
            "violet",
            "Brown",
            "Cream",
            "green",
            "gold",
            "pink"
        ],
        //data: [12, 19, 25, 17, 28, 24, 7,10,11,12,13,14,15,16,17,18,19,20,25,30]
        data:dataArray
        }]
      }
      });
  </script>

@parent
@endsection