<?php
$mValsArray = array("All Mythologies");
$cValsArray = array("All Classes");
$sValsArray = array("No Sorting");

require PRIVATE_PATH . '/connect_database.php';
$query = "SELECT title FROM mythologies";
$stmt = $connection->prepare($query);
$stmt->execute();
$stmt->bind_result($mythTitle);
while(!is_null($stmt->fetch())){
  array_push($mValsArray,$mythTitle);
}
$stmt->close();
mysqli_close($connection);

require PRIVATE_PATH . '/connect_database.php';
$query = "SELECT title FROM animalClasses";
$stmt = $connection->prepare($query);
$stmt->execute();
$stmt->bind_result($classTitle);
while(!is_null($stmt->fetch())){
  array_push($cValsArray,$classTitle);
}
$stmt->close();
mysqli_close($connection);

require PRIVATE_PATH . '/connect_database.php';
$query = "SELECT title FROM sortBy";
$stmt = $connection->prepare($query);
$stmt->execute();
$stmt->bind_result($sortTitle);
while(!is_null($stmt->fetch())){
  array_push($sValsArray,$sortTitle);
}
$stmt->close();
mysqli_close($connection);
?>
<form class="bg-dark w-100" name="searchForm" id="searchForm" method="get" action="/mythical-pets/public/pages/search_results.php">
  <div class="container pt-2">
    <div class="row">
      <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle m-1" id="sortTitle" type="button" name="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php if(isset($_GET['sortBy'])){echo $sValsArray[$_GET['sortBy']];} else{echo "Sort By";} ?>
        </button>
        <div class="dropdown-menu" id="sortDropdown">
          <?php
          for($i=0;$i<count($sValsArray);$i++){ ?>
            <button class="dropdown-item" type="button" onclick="setCatSort(this)" value=<?php echo $i ?> name="button"><?php echo $sValsArray[$i] ?></button>
          <?php } ?>
        </div>
      </div>
      <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle m-1" id="mythTitle" type="button" name="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php if(isset($_GET['mythology'])){echo $mValsArray[$_GET['mythology']];} else{echo "Mythology";} ?>
        </button>
        <div class="dropdown-menu" id="mythDropdown">
          <?php
          for($i=0;$i<count($mValsArray);$i++){ ?>
            <button class="dropdown-item" type="button" onclick="setCatMyth(this)" value=<?php echo $i ?> name="button"><?php echo $mValsArray[$i] ?></button>
          <?php } ?>
        </div>
      </div>
      <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle m-1" id="classTitle" type="button" name="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php if(isset($_GET['animalClass'])){echo $cValsArray[$_GET['animalClass']];}else{echo "Animal Class";} ?>
        </button>
        <div class="dropdown-menu">
          <?php
          for($i=0;$i<count($cValsArray);$i++){ ?>
            <button class="dropdown-item" type="button" onclick="setCatClass(this)" value=<?php echo $i ?> name="button"><?php echo $cValsArray[$i] ?></button>
          <?php } ?>
        </div>
      </div>
      <div class="col-5">
        <div class="form-group">
          <input type="text" name='itemSearch' value='<?php if(isset($_GET['itemSearch'])){echo $_GET['itemSearch'];}?>' class="form-control form-control-lg" id="itemSearch" aria-describedby="search" placeholder="Search for an item">
          <input type="hidden" id='sortIn' name="sortBy" value=<?php if(isset($_GET['sortBy'])){echo $_GET['sortBy'];}else{echo "0";}?>>
          <input type="hidden" id='mythIn' name="mythology" value=<?php if(isset($_GET['mythology'])){echo $_GET['mythology'];}else{echo "0";}?>>
          <input type="hidden" id='classIn' name="animalClass" value=<?php if(isset($_GET['animalClass'])){echo $_GET['animalClass'];}else{echo "0";}?>>
        </div>
      </div>
      <div class="col px-0">
        <button type="submit" class="btn btn-outline-primary btn-lg">Search</button>
      </div>
    </div>
  </div>
</form>

<script type="text/javascript">
function setCatSort(item){
  var sel = $(item).text();
  var selVal = $(item).val();
  $("#sortTitle").text(sel);
  $("#sortIn").val(selVal);
  if(window.location.href.includes("/pages/search_results.php")){
    $("#searchForm").submit();
  }
}
function setCatMyth(item){
  var sel = $(item).text();
  var selVal = $(item).val();
  $("#mythTitle").text(sel);
  $("#mythIn").val(selVal);
  if(window.location.href.includes("/pages/search_results.php")){
    $("#searchForm").submit();
  }
}
function setCatClass(item){
  var sel = $(item).text();
  var selVal = $(item).val();
  $("#classTitle").text(sel);
  $("#classIn").val(selVal);
  if(window.location.href.includes("/pages/search_results.php")){
    $("#searchForm").submit();
  }
}
</script>
