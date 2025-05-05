/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */

$(document).ready(function() {
    $("#raaagh").click(function() {
        $.ajax({
            url: 'ajax.php', //This is the current doc
            type: "POST",
            data: ({name: 145}),
            success: function(data) {
                console.log(data);
                $.ajax({
                    url:'ajax.php',
                    data: data,
                    dataType:'json',
                    success:function(data1) {
                        var y1=data1;
                        console.log(data1);
                    }
                });
            }
        });
    });
});
