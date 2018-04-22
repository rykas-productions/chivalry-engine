<div class="modal fade" id="message">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo "Sending {$r['username']} a Message"; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="success"></div>
                <div id="result"></div>
                <form id="mailpopupForm" name="mailpopupForm" action="js/script/sendmail.php">
                    <div class="form-group">
                        <div id="result"></div>
                        <label for="recipient-name" class="control-label"><?php echo "Recipient"; ?></label>
                        <input type="text" class="form-control" name="sendto" required="1" value="<?php echo $r['username']; ?>" id="recipient-name">
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="control-label"><?php echo "Message"; ?></label>
                        <textarea class="form-control" name="msg" required="1" id="message-text"></textarea>
                    </div>
            </div>
            <div class="modal-footer">
                <?php
                    $thiscode=request_csrf_html("inbox_send");
                    echo $thiscode;
                ?>
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo "Close Window"; ?></button>
                <input type="submit" value="<?php echo "Send Message"; ?>" id="sendmessage" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>