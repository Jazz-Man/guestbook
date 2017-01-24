<?php
    $my_messages = new GB\GB_Messages_List();
    if ( ! empty($my_messages->table_data())) {
        $my_messages->prepare_items();
        $my_messages->display();
    }