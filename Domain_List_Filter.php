<?php
function filter_input_text($text) {
    $text = trim($text);
    $text = filter_var($text, FILTER_SANITIZE_STRING);
    $text = preg_replace('/[^a-zA-Z0-9._-]/', '', $text);
    $text = preg_replace('/^www\./i', '', $text);
    return $text;
}

if(isset($_POST['submit'])) {
    $list_a = $_POST['list_a'];
    $list_b = $_POST['list_b'];

    $list_a_array = array_filter(explode("\n", $list_a), 'trim');
    $list_b_array = array_filter(explode("\n", $list_b), 'trim');

    $list_a_filtered = array_map('filter_input_text', $list_a_array);
    $list_b_filtered = array_map('filter_input_text', $list_b_array);

    $list_a_filtered = array_map('strtolower', $list_a_filtered);
    $list_b_filtered = array_map('strtolower', $list_b_filtered);

    $list_a_filtered = array_unique($list_a_filtered);
    $list_b_filtered = array_unique($list_b_filtered);

    $list_one = array_diff($list_a_filtered, $list_b_filtered);
    $list_two = array_diff($list_b_filtered, $list_a_filtered);
    $list_three = array_intersect($list_a_filtered, $list_b_filtered);
    $list_four = array_unique(array_merge($list_a_filtered, $list_b_filtered));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>List Filter</title>
</head>
<body>
    <h1>Domain List Filter</h1>
	removing www and any spaces or dodgy code, compares 2 lists of domains

    <form method="POST">
        <label for="list_a">List A:</label>
        <textarea name="list_a" rows="10" cols="30"><?php if(isset($_POST['list_a'])) echo $_POST['list_a']; ?></textarea>
        <br>
        <label for="list_b">List B:</label>
        <textarea name="list_b" rows="10" cols="30"><?php if(isset($_POST['list_b'])) echo $_POST['list_b']; ?></textarea>
        <br>
        <input type="submit" name="submit" value="Filter">
    </form>
    <?php if(isset($_POST['submit'])) { ?>
        <h2>In List A only</h2>
        <ul>
            <?php foreach($list_one as $item) { ?>
                <li><?php echo $item; ?></li>
            <?php } ?>
        </ul>
        <h2>In List B only</h2>
        <ul>
            <?php foreach($list_two as $item) { ?>
                <li><?php echo $item; ?></li>
            <?php } ?>
        </ul>
        <h2>In Both Lists</h2>
        <ul>
            <?php foreach($list_three as $item) { ?>
                <li><?php echo $item; ?></li>
            <?php } ?>
        </ul>
        <h2>Lists combined with duplicates removed</h2>
        <ul>
            <?php foreach($list_four as $item) { ?>
                <li><?php echo $item; ?></li>
            <?php } ?>
        </ul>
    <?php } ?>
</body>
</html>
