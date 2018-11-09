<!DOCTYPE html>
<?php

date_default_timezone_set("America/New_York");

include("database.php");

$db = db_connect();

if(isset($_POST["submit"])) {

    $file_name = $_FILES["file"]["name"];
    $file_size = $_FILES["file"]["size"];
    $tmp_name = $_FILES['file']['tmp_name'];
    $date = date("Y/m/d");
    $time = date("h:i:sa");
    $timestamp = time();

    $sql = "INSERT INTO files ";
    $sql .= "(filename, date_created, time_created, date_accessed, time_accessed, timestamp, filesize) ";
    $sql .= "VALUES (";
    $sql .= "'" . $file_name . "',";
    $sql .= "'" . $date . "',";
    $sql .= "'" . $time . "',";
    $sql .= "'" . $date . "',";
    $sql .= "'" . $time . "',";
    $sql .= "'" . $timestamp . "',";
    $sql .= "'" . $file_size . "'";
    $sql .= ")";

    $result = mysqli_query($db, $sql);

    if($result) {
        echo "<div style='width:100%;background-color:greenyellow;display:flex;border:1px solid black;margin-bottom:5px'>" . "<p style='margin:auto'>Upload Successful!</p>" . "</div>";
    } else {
        echo "<div style='width:100%;background-color:crimson;display:flex;border:1px solid black;margin-bottom:5px'>" . "<p style='margin:auto'>Duplicate Entry Detected</p>" . "</div>";
    }

    if (move_uploaded_file($tmp_name, "uploads/".$file_name)) {
        //
    } else {
        //
    }

}
function find_all_uploads() {
    global $db;
    $sql = "SELECT * FROM files ";
    $sql .= "ORDER BY id DESC";
    $result = mysqli_query($db, $sql);
    return $result;
}

$uploads_array = find_all_uploads();

?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href='style.css'>
        <title>Timestamp App</title>
        <style>

        </style>
    </head>
    <body>
        <div style="width:15px;height:15px;border:1px solid black;background-color:greenyellow;border-radius:100%;display:inline-block;margin-left:22px"></div>
        <p id="newp" style="font-size:15px;display:inline-block">: Accessed Earlier</p>
        <div style="width:15px;height:15px;border:1px solid black;background-color:red;border-radius:100%;display:inline-block;margin-left:5px"></div>
        <p id="newp" style="font-size:15px;display:inline-block">: Accessed Later</p>
        <div id="outer">
            <form id="form" action="" method="POST" enctype="multipart/form-data">
                <input type="file" id="file" name="file">
                <input type="submit" id="submit" name="submit" value="Upload">
            </form>

            <div id="container">

                <table class="list">
                    <tr>
                        <th>ID</th>
                        <th>Filename</th>
                        <th>Date Created</th>
                        <th>Time Created</th>
                        <th>Date Accessed</th>
                        <th>Time Accessed</th>
                        <th>Timestamp</th>
                    </tr>

                    <?php while($file = mysqli_fetch_assoc($uploads_array)) { ?>
                        <tr class='row'>
                            <td class='id'> <?php echo $file['id']; ?> </td>
                            <td class='filename'> <?php echo $file['filename']; ?> </td>
                            <td class='dateCreated'> <?php echo $file["date_created"]; ?> </td>
                            <td class='timeCreated'> <?php echo $file["time_created"]; ?> </td>
                            <td class='dateAccessed'> <?php echo $file["date_accessed"]; ?> </td>
                            <td class='timeAccessed'> <?php echo $file["time_accessed"]; ?> </td>
                            <td class='timestamp'> <?php echo $file["timestamp"]; ?> </td>
                            <td class='filesize' style='display:none'> <?php echo $file["file_size"]; ?></td>
                        </tr>
                    <?php } ?>

                </table>
                <?php db_disconnect($db); ?>
            </div>
            <div id="canvas"><img id="img" src=""></div>
            <div id="content">
                <div id="filename_text"></div>
                <div id="file_data"></div>
            </div>

        </div>
        <script>

            $("tr").click(function() {
                var timestamp = $(this).find(".timestamp").text();
                var rows = $(this).parent().children();
                var children = $(this).parent().children().find(".timestamp");


                $(rows).removeClass("bigger, selected");
                $(rows).removeClass("smaller");
                $(this).addClass("selected");
                $(children).each(function() {
                    if($(this).text() < timestamp) {
                        $(this).parent().addClass("bigger");
                    } else if($(this).text() > timestamp) {
                        $(this).parent().addClass("smaller");
                    }
                })
            })
            $("tr").click(function() {
                var thisRow = $(this);
                $("#download").remove();
                $("#show_older").remove();
                $("#show_newer").remove();
                $("#show_all").remove();
                $("#displayOnly").remove();
                $("#remove").remove();
                var filename = $(this).find(".filename").text().substring(1);
                var datecreated = $(this).find(".dateCreated").text();
                var timecreated = $(this).find(".timeCreated").text();
                var dateaccessed = $(this).find(".dateAccessed").text();
                var timeaccessed = $(this).find(".timeAccessed").text();
                var filesize = $(this).find(".filesize").text();
                var timestamp = $(this).find(".timestamp").text();
                var img = document.getElementById("img");
                var string = "uploads/" + filename;
                var buttonstring = "<a id='link' style='width:100%;height:100%' href='uploads/"+filename+"' download>";
                var older = 0;
                var newer = 0;

                $(".row").each(function() {
                    var stamp = $(this).find(".timestamp").text();
                    var thisTimestamp = parseInt(stamp);

                    if(thisTimestamp > timestamp) {
                        newer++;
                    } else if(thisTimestamp < timestamp) {
                        older++;
                    }
                })

                img.src=string;
                $("#filename_text").html(filename);
                $("#file_data").html(

                        "<p>Size:" + filesize + " bytes</p>" +
                        "<p>Date Created: " + datecreated + "</p>" +
                        "<p>Time Created: " + timecreated + "</p>" +
                        "<p>Date Accessed: " + dateaccessed + "</p>" +
                        "<p>Time Accessed: " + timeaccessed + "</p>" +
                        "<p>Timestamp: " + timestamp + "</p>" +
                        "<p>Earlier: <span style='color:green'>" + older + "</span>" + ", " + "Later: <span style='color:red'>" + newer + "</span></p>"
                );
                $("#content").append("<button id='download' style='width:75.3px;height:75.3px'>" + buttonstring + "<i class='material-icons' style='font-size:25px'>" + "&#xe2c4;" + "</i></button>");
                $("#content").append("<button id='remove'>Remove</button>")
                $("#content").append("<p id='displayOnly' style='font-size:20px;color:whitesmoke;float:left;margin:8px;margin-left:10px'>" + "Show: " + "</p>");
                $("#content").append("<button id='show_older'>Earlier</button>");
                $("#content").append("<button id='show_newer'>Later</button>");
                $("#content").append("<button id='show_all'>All</button>");

                $("#show_older").click(function() {
                    $(".row").each(function() {
                        $(this).show();
                    });
                    $(".row").each(function() {
                        var stamp = $(this).find(".timestamp").text();
                        var thisTimestamp = parseInt(stamp);
                        if(thisTimestamp > timestamp) {
                            $(this).hide();
                        }
                    })
                })
                $("#show_newer").click(function() {
                    $(".row").each(function() {
                        $(this).show();
                    });
                    $(".row").each(function() {
                        var stamp = $(this).find(".timestamp").text();
                        var thisTimestamp = parseInt(stamp);
                        if(thisTimestamp < timestamp) {
                            $(this).hide();
                        }
                    })
                })
                $("#show_all").click(function() {
                    $(".row").each(function() {
                        $(this).show();
                    })
                })
                $("#remove").click(function() {
                    $.ajax({
                        type: "POST",
                        url: "remove.php",
                        data: {
                            "filename": filename,
                        },
                        success: function() {
                            $(thisRow).remove();
                        }

                    })
                })
                $("#link").click(function() {
                    var year = new Date().getFullYear();
                    var month = new Date().getMonth() + 1;
                    var day = new Date().getDate();
                    var newDateAccessed = year + "-" + month + "-" + day;

                    var hours = new Date().getHours();
                    var minutes = new Date().getMinutes();
                    var seconds = new Date().getSeconds();
                    var newTimeAccessed = hours + ":" + minutes + ":" + seconds;

                    var newTimeStamp = Math.floor(Date.now()/1000);

                    $.ajax({
                        type: "POST",
                        url: "update.php",
                        data: {
                            "filename": filename,
                            "newDateAccessed": newDateAccessed,
                            "newTimeAccessed": newTimeAccessed,
                            "newTimeStamp": newTimeStamp
                        },
                        success: function(data) {
                            $(thisRow).find(".dateAccessed").html(newDateAccessed);
                            $(thisRow).find(".timeAccessed").html(newTimeAccessed);
                            $(thisRow).find(".timestamp").html(newTimeStamp);
                            $(thisRow).trigger("click");
                        }

                    })

                })
            })

        </script>
    </body>
</html>
