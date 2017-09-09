/*
 * MWS Admin v2.1 - Dashboard Demo JS
 * This file is part of MWS Admin, an Admin template build for sale at ThemeForest.
 * All copyright to this file is hold by Mairel Theafila <maimairel@yahoo.com> a.k.a nagaemas on ThemeForest.
 * Last Updated:
 * December 08, 2012
 *
 */

;(function( $, window, document, undefined ) {

    $(document).ready(function() {
        if( $.plot ) {
            var s0=[];
            var s1=moneyJson;
            var s2=[];
            var snum=moneyJson.length;
            var sleave= parseInt(moneyJson[snum-1][0])+1;
            for(var i=1;i<moneyJson[0][0];i++){
                var dbefore=[i,0];
                s0.push(dbefore);
            }
            for(var k=sleave;k<=days;k++){
                var dafter=[k,0];
                s2.push(dafter);
            }
            var Sales=s0.concat(s1,s2);
            console.log(Sales);
            var data = [{
                data: Sales,
                label: "销售额",
                color: "#c5d52b"
            }];
            var plot = $.plot($("#mws-dashboard-chart"), data, {
                series: {
                    lines: {
                        show: true
                    },
                    points: {
                        show: true
                    }
                },
                tooltip: true,
                grid: {
                    hoverable: true,
                    borderWidth: 0
                }
            });
        }
        if( $.fn.wizard ) {
            $('#mws-wizard-form').wizard({
                element: 'fieldset', 
                buttonContainerClass: 'mws-button-row'
            });
        }

        // Data Tables
        if( $.fn.dataTable ) {
            $(".mws-datatable").dataTable({
                "aoColumns": [
                    null, 
                    null,
                    null, 
                    null, 
                    null, 
                    { "bSortable": false }
                ]
            });
        }
            var d0=[];
            var d1=totalJson;
            var d2=[];
            var num=totalJson.length;
            var leave= parseInt(totalJson[num-1][0])+1;

            for(var i=1;i<totalJson[0][0];i++){
                var dbefore=[i,0];
                d0.push(dbefore);
            }
            for(var k=leave;k<=days;k++){
                var dafter=[k,0];
                d2.push(dafter);
            }
            var d=d0.concat(d1,d2);
            var stack = 0,
                bars = true,
                lines = false,
                steps = false;

            $.plot($("#mws-bar-chart"), [d], {
                series: {
                    stack: stack,
                    lines: {
                        show: lines,
                        fill: true,
                        steps: steps
                    },
                    bars: {
                        show: bars,
                        barWidth:0.4
                    }
                }, 
                grid: {
                    borderWidth: 0
                }
            });
    });

}) (jQuery, window, document);


  /* 月份日期函数 
   var year;
        var month;
        var day;
        var date=new Date();
            year =date.getFullYear();
            month=date.getMonth()+1;
            day  =date.getDate();
          getDayNum(year);
        function getDayNum(year){
            var dataNumList=[];
            var dataNums=[];
            for(var j=1;j<=12;j++){
                if(j==2){
                    if(year%4==0){
                        dataNums=[j,29];
                    }else{  
                        dataNums=[j,28];
                    }
                    dataNumList.push(dataNums);
                    continue;
                }
                if(j==4||j==6||j==9||j==11){
                    dataNums=[j,30];
                    dataNumList.push(dataNums);
                    continue;
                }
                if(j==1||j==3||j==5||j==7||j==8||j==10||j==12){
                    dataNums=[j,31];
                    dataNumList.push(dataNums);
                }
            }
             console.log(dataNumList);
             console.log(dataNumList[0][1]);
        }*/