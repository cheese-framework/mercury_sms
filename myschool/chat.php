<?php
include_once './includes/header.php';
?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row mx-auto">
                <div class="col-lg-9">
                    <div class="card">
                        <p id="user_id" style="display: none;"><?php echo $sms_userId; ?></p>
                        <p id="school_id" style="display: none;"><?php echo $schoolId; ?></p>
                        <div class="card-header">
                            <h3>Chatroom</h3>
                        </div>
                        <div class="card-body" id="messages_area" style="background-color: #f5f5f5; height: 60vh; overflow-y: auto;">

                        </div>
                    </div>
                    <form method=" post" id="chat_form">
                        <div class="input-group mb-3">
                            <textarea name="chat_message" id="chat_message" placeholder="Start Typing..." class="form-control" data-parsley-maxlength="1000" required></textarea>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary" id="send" name="send"><i class="fa fa-paper-plane"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-3"></div>
            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>
        <script>
            var conn = new WebSocket('ws://localhost:8080');
            conn.onopen = function(e) {
                console.log("Connection established!");
            };

            conn.onerror = function(e) {
                alert("Connection to chat server is lost");
                // console.log(e);
            }

            conn.onmessage = function(e) {
                console.log(e.data);
                let data = JSON.parse(e.data);
                let fromClass = '';
                let backgroundColorFrom = '';

                if (data.from == "Me") {
                    fromClass = "row justify-content-end";
                    backgroundColorFrom = 'text-dark alert-success';
                } else {
                    fromClass = "row justify-content-start";
                    backgroundColorFrom = 'text-dark alert-warning';
                }


                let htmlData =
                    `<div class='${fromClass}'>
                    <div class='col-sm-8'>
                        <div class='shadow-sm alert ${backgroundColorFrom} card'>
                        <div class='card-header text-right alert ${backgroundColorFrom}'> <b>${data.from}</b></div>
                        <div class='card-body alert ${backgroundColorFrom} '>${data.msg}</div>
                        <br>
                        <div class='card-footer alert ${backgroundColorFrom}  text-right'>
                            <small><i>${data.date}</i></small>
                        </div>
                        </div>
                    </div>
                </div>`;

                $("#messages_area").append(htmlData);
                $("#chat_message").val("");
                let messageBody = document.getElementById("messages_area");
                messageBody.scrollTop = messageBody.scrollHeight - messageBody.clientHeight;
            };



            $('#chat_form').submit(function(e) {
                e.preventDefault();
                let message = $("#chat_message").val();
                if (message.trim() != "") {
                    let userId = $("#user_id").text();
                    let schoolId = $("#school_id").text();
                    let data = {
                        userId: userId,
                        msg: message,
                        school: schoolId
                    };
                    conn.send(JSON.stringify(data));


                    // let messageBody = document.getElementById("messages_area");
                    // messageBody.scrollTop = messageBody.scrollHeight - messageBody.clientHeight;
                }

            });
        </script>