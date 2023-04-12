<?php
/**
 * Block for total progress
 *
 * @package   block_html
 * @copyright 1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use block_offthejob\lib;

class block_offthejob extends block_base {
    
    function init(){
        $this->title = 'Total Progress';
    }

    function get_content(){
        if ($this->content !== NULL){
            return $this->content;
        }
        $lib = new lib;
        $id = $_GET['id'];
        if($lib->setup_exists($id) == true){
            $values = $lib->get_completion_percent($id);
            $value1 = 0;
            if($values[0] !== 0){
                $value1 = $values[1] / $values[0];
            }
            $value2 = 0;
            if($values[3] !== 0){
                $value2 = $values[3] / $values[2];
            }
            $percent = (($value1 + $value2) / 2);
            $percent = $percent * 100;
            $percenttxt = round($percent * 100);
            $incomplete = round(100 - $percenttxt);

            $expectedhrs = $lib->get_percent_expect_hours($id);
            $expectedhr = ($expectedhrs / 100)/2;
            $expectedhrs = $expectedhrs / 2;

            $this->content = new stdClass();
            $this->content->text = "
                <div id='totalprogressdiv'>
                <canvas id='canvas' width='100px' height='100px'></canvas>
                <div>
                <p>Completed: $percenttxt%</p>
                <p>Expected Hours: $expectedhrs% </p>
                <p>Incomplete: $incomplete% </p>
                </div>
                </div>
                <script>
                    let canvas = document.getElementById('canvas');
                    let ctx = canvas.getContext('2d');
                    let int = 2;
                    let percent = int * $percent;
                    let expected = int * $expectedhr;
                    ctx.lineWidth = 25;
                    ctx.beginPath();
                    ctx.strokeStyle = 'red';
                    ctx.arc(50, 50, 25, 0, percent*Math.PI,int*Math.PI);
                    ctx.stroke();
                    ctx.beginPath();
                    ctx.strokeStyle = 'orange';
                    ctx.arc(50, 50, 25, 0, expected*Math.PI);
                    ctx.stroke();
                    ctx.beginPath();
                    ctx.strokeStyle = 'green';
                    ctx.arc(50, 50, 25, 0, percent*Math.PI);
                    ctx.stroke();

                </script>
                <style>
                    #totalprogressdiv{
                        display: flex;
                    }
                </style>
                ";
                $this->content->footer = "";
            return $this->content;
        }
    }
}