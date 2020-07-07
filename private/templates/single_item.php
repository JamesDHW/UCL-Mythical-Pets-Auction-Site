<!DOCTYPE html>
<html>
    <?php require TEMPLATE_PATH . '/html_head.php'; ?>
    <body>
        <div class="container mt-3">
            <?php if (isset($_GET['errorMessage'])) : ?>
                <div class="alert alert-warning" role="alert">
                    <?php echo $errorMessage ?>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-sm">
                    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">
                            <?php if ($hasPictures) : ?>
                                <?php $active = 'active'; ?>
                                <?php foreach ($pictures as $picture) : ?>
                                    <div class="carousel-item <?php echo $active; ?>">
                                        <img src="../images/<?php echo $_GET['itemID'] . "/$picture"; ?>" class="d-block w-100" alt="Item Image">
                                        <?php $active = ''; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div class="carousel-item active">
                                    <img src="../images/placeholder.jpeg" class="d-block w-100" alt="Item Image">
                                </div>
                            <?php endif; ?>
                        </div>
                        <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
                <div class="col-sm">
                    <div class="row">
                        <div class="col-sm">
                            <h3><?php echo $name; ?></h3>
                            <p class="text-info"><?php echo "$mythology | $animalClass"; ?></p>
                        </div>
                        <div class="col-sm align-self-end">
                            <?php if ($loggedIn && !$isSeller & ($hasStarted & !$hasEnded)) : ?>
                                <?php if ($followedItem) : ?>
                                    <form action="../forms/untrackItem.php" method="get">
                                        <input name="itemID" type="hidden" value="<?php echo $_GET['itemID']; ?>">
                                        <button class="btn btn-primary">Do not track Item</button>
                                    </form>
                                <?php else : ?>
                                    <form action="../forms/trackItem.php" method="get">
                                        <input name="itemID" type="hidden" value="<?php echo $_GET['itemID']; ?>">
                                        <button class="btn btn-primary">Track Item</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <hr/>
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col-sm">
                                <p class="text-secondary">Current price:</p>
                            </div>
                            <div class="col-sm">
                                <p id="currentPrice">£<?php echo $currentPrice; ?></p>
                                <p class="text-info" id="bidType">
                                    <?php
                                        if ($isStartingPrice) {
                                            echo 'Starting Price';
                                        } elseif ($userBidWinning) {
                                            echo 'Your bid is winning!';
                                        }
                                    ?>
                                </p>
                            </div>
                            <div class="col-sm">
                                <form action="../forms/place_bid.php" method="post">
                                    <div class="form-group">
                                        <label for="label_place_bid">Your price:</label>
                                        <input type="text" name="bidValue" class="form-control" id="input_place_bid" aria-describedby="bid Price" placeholder="Enter your bid">
                                        <small id="help_place_bid" class="form-text text-muted">Enter your bid in £.</small>
                                        <input type="hidden" name="itemID" value="<?php echo $_GET['itemID']; ?>" >
                                    </div>
                                    <button type="submit" id="placeBidButton" class="btn btn-primary d-block mx-auto <?php
                                    if (!$loggedIn || $isSeller || $hasEnded || !$hasStarted) { echo 'disabled'; } ?>">Submit</button>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm">
                                <p class="text-secondary" id="auctionStatus">
                                    <?php
                                        if ($hasStarted && !$hasEnded) {
                                            echo 'Ends in: ';
                                        } elseif (!$hasStarted) {
                                            echo 'Starts in: ';
                                        }
                                    ?>
                                </p>
                            </div>
                            <div class="col-sm">
                                <p id="auctionTime"><?php
                                        if (!$hasStarted) {
                                            echo $toStart;
                                        } else {
                                            echo $toEnd;
                                        }
                                    ?></p>
                            </div>
                        </div>
                        <hr/>
                        <div class="row pb-2">
                            <div class="col-sm">
                                <p class="text-secondary">Buy now price:</p>
                            </div>
                            <div class="col-sm">
                                <p>£<?php echo $buyNowPrice; ?></p>
                            </div>
                            <div class="col-sm">
                                <form action="../forms/buy_now.php" method="post">
                                    <button type="submit" id="buyNowButton" class="btn btn-primary d-block mx-auto <?php
                                    if (!$loggedIn || $isSeller || $hasEnded || !$hasStarted ||
                                    (float)$currentPrice >= (float)$buyNowPrice) { echo 'disabled'; } ?>">Buy now</button>
                                    <input type="hidden" name="itemID" value="<?php echo $_GET['itemID']; ?>">
                                </form>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-sm">
                                <p class="text-secondary">Description:</p>
                            </div>
                            <div class="col-sm">
                                <p><?php echo $description; ?></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                          <div class="col-sm">
                            <label class="mb-1 text-secondary">Recent Bids:</label>
                            <table class="table table-hover table-striped" id="bidsTable">
                              <thead class = "thead-light">
                                <tr>
                                  <th>Bid Value</th>
                                  <th>Bid Timestamp</th>
                                  <th>Bidder </th>
                                </tr>
                              </thead>
                                <?php if($result -> num_rows > 0){
                                  while($row = $result -> fetch_assoc()){
                                    echo "<tr><td class=\"align-middle\">£" . $row["bidvalue"] . "</td><td class=\"align-middle\">" .
                                    $row["timestamp"] . "</td><td class=\"align-middle\">" . $row["firstName"] . "</td></tr>";
                                  }
                                }
                                ?>
                            </table>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr/>
            <h4>Things you might be interested in:</h4>
            <div class="row mb-3">
                <?php foreach ($recommendations as $item) : ?>
                    <div class="col-2 border p-2">
                            <h5><?php echo $item['itemName'] ?></h5>
                            <a href="item.php?itemID=<?php echo $item['itemID']; ?>">Check out now!</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php require PUBLIC_PATH . "/forms/update_item_views.php"; ?>
        <script src="../js/refresh_item.js"></script>
    </body>
</html>
