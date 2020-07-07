<!doctype html>
<html lang="en">
<?php require_once 'html_head.php'; ?>
<body>
<?php
    require_once 'nav.php';
    require_once 'searchbar.php';
?>

<div class="container mt-3 mb-3">
    <div class="alert alert-warning text-center" role="alert" id="emptyFieldError" style="display: none">
        Please fill out all the fields.
    </div>
    <div class="alert alert-warning text-center" role="alert" id="addItemError" style="display: none">
        Your item could not be added.
    </div>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Add an Item</h5>
            <form id="addItemForm" enctype="multipart/form-data">
                <div class="from-group mb-2">
                    <label for="addTitle">Title</label>
                    <input id="addTitle" class="form-control" type="text" name="title" value="" placeholder="Enter title">
                </div>
                <div class="from-group mb-2">
                    <label for="addMythology">Mythology</label>
                    <select name="mythology" id="addMythology" class="form-control">
                        <?php foreach ($mythologies as $mythology) : ?>
                            <option><?php echo $mythology; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="from-group mb-2">
                    <label for="addAnimalClass">Animal Class</label>
                    <select name="animalClass" id="animalClass" class="form-control">
                        <?php foreach ($animalClasses as $animalClass) : ?>
                            <option><?php echo $animalClass; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="from-group mb-2">
                    <label for="addDescription">Description</label>
                    <textarea id="addDescription" name="description" rows="3" class="form-control" placeholder="Enter description"></textarea>
                </div>
                <div class="from-group mb-2">
                    <label for="addBuyNowPrice">Buy Now Price</label>
                    <input id="addBuyNowPrice" class="form-control" type="number" name="buyNowPrice" value="" placeholder="Enter buy now price" min="1" step="0.01">
                </div>
                <div class="from-group mb-2">
                    <label for="addStartingPrice">Starting Price</label>
                    <input id="addStartingPrice" class="form-control" type="number" name="startingPrice" value="" placeholder="Enter starting price" min="1" step="0.01">
                </div>
                <div class="from-group mb-2">
                    <label for="addStartTime">Start Time</label>
                    <input class="form-control" type="text" name="startTime" value="" id="addStartTime">
                </div>
                <div class="from-group mb-2">
                    <label for="addEndTime">End in (days)</label>
                    <input class="form-control" type="number" name="endTime" value="" id="addEndTime" min="1">
                </div>
                <div class="form-group mb-2">
                    <label for="addPicture">Upload a picture</label>
                    <input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
                    <input id="addPictures" name="pictures[]" type="file" class="form-control-file" accept=".jpg,.jpeg,.png" multiple="multiple">
                </div>
                <button type="submit" class="btn btn-primary mt-3" id="addItemButton">Submit</button>
            </form>
        </div>
    </div>
</div>

<script src="../js/jquery.datetimepicker.full.min.js"></script>
<script src="../js/add_item.js"></script>
</body>
</html>
