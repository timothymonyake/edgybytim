<!DOCTYPE html>
<html>
<head>
    <title>Trade Table Debug</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Trade Table Debug Tool</h1>
    
    <h2>Test AJAX Endpoint</h2>
    <button id="testAjax">Test /trades/data Endpoint</button>
    <pre id="ajaxResult"></pre>
    
    <h2>Test with Different Filters</h2>
    <button id="testAllTime">Test All Time</button>
    <button id="testThisMonth">Test This Month</button>
    <pre id="filterResult"></pre>
    
    <script>
        $('#testAjax').click(function() {
            $.ajax({
                url: '/trades/data',
                type: 'GET',
                data: {
                    start_date: '01/01/2020',
                    end_date: '31/12/2025',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#ajaxResult').text(JSON.stringify(response, null, 2));
                },
                error: function(xhr) {
                    $('#ajaxResult').text('ERROR: ' + xhr.status + '\n' + xhr.responseText);
                }
            });
        });
        
        $('#testAllTime').click(function() {
            $.ajax({
                url: '/trades/data',
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#filterResult').text('Total Records: ' + response.recordsTotal + '\n' + JSON.stringify(response, null, 2));
                },
                error: function(xhr) {
                    $('#filterResult').text('ERROR: ' + xhr.status + '\n' + xhr.responseText);
                }
            });
        });
        
        $('#testThisMonth').click(function() {
            let now = new Date();
            let start = new Date(now.getFullYear(), now.getMonth(), 1);
            let end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            
            let startStr = ('0' + start.getDate()).slice(-2) + '/' + ('0' + (start.getMonth() + 1)).slice(-2) + '/' + start.getFullYear();
            let endStr = ('0' + end.getDate()).slice(-2) + '/' + ('0' + (end.getMonth() + 1)).slice(-2) + '/' + end.getFullYear();
            
            $.ajax({
                url: '/trades/data',
                type: 'GET',
                data: {
                    start_date: startStr,
                    end_date: endStr,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#filterResult').text('This Month (' + startStr + ' - ' + endStr + ')\nTotal Records: ' + response.recordsTotal + '\n' + JSON.stringify(response, null, 2));
                },
                error: function(xhr) {
                    $('#filterResult').text('ERROR: ' + xhr.status + '\n' + xhr.responseText);
                }
            });
        });
    </script>
</body>
</html>
