<div class="modal fade" id="cash">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo "Sending Cash to {$r['username']}"; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="successcash"></div>
                <div id="result2"></div>
                <form id="cashpopupForm" name="cashpopupForm" action="js/script/sendcash.php">
                    <div class="form-group">
                        <div id="result"></div>
                        <input type="hidden" class="form-control" name="sendto" required="1" value="<?php echo $_GET['user']; ?>" id="recipient-name">
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="control-label"><?php echo "Amount"; ?></label>
                        <input type='number' min='0' max="<?php echo $ir['primary_currency']; ?>" class="form-control" name="cash" required="1" id="message-text">
                    </div>
            </div>
            <div class="modal-footer">
                <?php
                    $code=request_csrf_html("cash_send");
                    echo $code;
                ?>
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo "Close Window"; ?></button>
                <input type="submit" value="<?php echo "Send Cash"; ?>" id="sendcash" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>