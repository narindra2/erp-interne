<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF</title>
</head>
<style type="text/css">
    .content {
        max-width: 600px;
        margin: auto;
        margin-bottom: 5%
    }
    table td img {
    display: inline-block;  
    float: left;
}
  
</style>
<body>
    <div class="content">
        
        @include('days_off.pdf.detail')
    </div>
    <div class="content">
        @include('days_off.pdf.detail')
    </div>
</body>
</html>
