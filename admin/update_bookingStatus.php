<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}
else{
if(isset($_POST['submit'])){

    $bid=$_GET['bid'];
    $estatus=$_POST['status'];
    $oremark=$_POST['officialremak'];
    $tbaleid=$_POST['table'];
    $bdate=$_POST['bdate'];
    $btime=strtotime($_POST['btime']);
    $endTime = date("H:i:s", strtotime('+30 minutes', $btime));
    
    $ret=$conn->prepare("SELECT * FROM `tblbookings` where ('$btime' BETWEEN time(bookingTime) and '$endTime' || '$endTime' BETWEEN time(bookingTime) and '$endTime' || bookingTime BETWEEN '$btime' and '$endTime') and tableId='$tbaleid' and bookingDate='$bdate' and boookingStatus='Accepted'");
    $ret->execute();
    if($ret->rowCount() > 0){
    echo "<script>alert('Table already booked for given Date and Time. please choose another table');</script>";
    } 
    else{
    $query=$conn->prepare("UPDATE `tblbookings` set adminremark=? ,boookingStatus=? ,tableId=?  where id=?");
    $query->execute([$oremark, $estatus, $tbaleid, $bid]);
    if($query){
    echo "<script>alert('Booking Details Updated   successfully.');</script>";
    //echo "<script type='text/javascript'> document.location = 'manage-classes.php'; </script>";
    } else {
    echo "<script>alert('Something went wrong. Please try again.');</script>";
    }
    
    }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>UpdateBookingStatus</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">
    

</head>
<body>

<?php include '../components/admin_header.php' ?>

<section class="Booking_details">
<h1 class="heading">Update Booking Status</h1>
</section>
    <form action="" method="POST" enctype="multipart/form-data">

        <p><select class="form-control" name="status" id="status" required>
            <option value="">Select Booking Status</option>
            <option value="Accepted">Accepted</option>
            <option value="Rejected">Rejected</option>
          </select></p>
        <p>
            <input type="hidden" name="bdate" value="<?php echo $date;?>">
            <input type="hidden" name="btime" value="<?php echo $btime;?>">
            <select class="form-control" name="table" id="table">
            <option value="">Select Table</option>
            <?php 
            $show_tables=$con->prepare("SELECT id,tableNumber from `tblrestables`");
            $show_tables->execute();
            while($fetch_table = $show_tables->fetch(PDO::FETCH_ASSOC)){
            ?>
                <option value="<?= $fetch_table['id'];?>"><?=$fetch_table['tableNumber'];?></option>
            <?php } ?> 
        </select></p>
          
          
          
        <p><textarea class="form-control" name="officialremak" placeholder="Official Remark" rows="5" required></textarea></p>
        <input type="submit" class="btn btn-primary" name="submit" value="update">

    </form> 
<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>

<?php } ?>