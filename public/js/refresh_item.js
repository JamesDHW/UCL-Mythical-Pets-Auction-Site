$(document).ready(() => {
    let urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('itemID')) {
        let itemID = urlParams.get('itemID');

        let getItemData = () => {
            $.post('../forms/async_item.php', {itemID: itemID}, (data) => {
                if (data.status === '1') {
                    let itemData = data.itemData;
                    let auctionStatus = itemData.auctionStatus;
                    let auctionStatusText = itemData.auctionStatusText;
                    let timeLeft = itemData.timeLeft;
                    let bidValue = itemData.bidValue;
                    let bidType = itemData.bidType;
                    let buyNow = itemData.buyNow;

                    $('#currentPrice').text('£' + bidValue);
                    $('#bidType').text(bidType);
                    $('#auctionStatus').text(auctionStatusText);
                    $('#auctionTime').text(timeLeft);

                    if (auctionStatus === '1') {
                        $('#placeBidButton').removeAttr('disabled');
                        $('#buyNowButton').removeAttr('disabled');
                    } else {
                        $('#placeBidButton').attr('disabled', true);
                        $('#buyNowButton').attr('disabled', true);
                    }
                    if (buyNow === '1') {
                        $('#buyNowButton').removeAttr('disabled');
                    } else {
                        $('#buyNowButton').attr('disabled', true);
                    }
                }
                // schedule the next update
                setTimeout(getItemData, 10000);
            });
        };
        setTimeout(getItemData, 5000);
    }
});