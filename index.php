<?php
    //  directory creation logic
    if(isset($_GET["create_dir"])){
        if($_GET["create_dir"] != ""){
            $dir_to_create = './' . $_GET["path"] . $_GET["create_dir"];
            if (!is_dir($dir_to_create)) mkdir($dir_to_create, 0777, true);
        }
        $url = preg_replace("/(&?|\??)create_dir=(.+)?/", "", $_SERVER["REQUEST_URI"]);
        header('Location: ' . urldecode($url));
    }

    // directory deletion logic
    if(isset($_POST['delete'])){
        $objToDelete = './' . $_GET["path"] . $_POST['delete']; 
        $objToDeleteEscaped = str_replace("&nbsp;", " ", htmlentities($objToDelete, null, 'utf-8'));
        if(is_file($objToDeleteEscaped)){
            if (file_exists($objToDeleteEscaped)) {
                unlink($objToDeleteEscaped);
            }
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>File System Browser</title>
    </head>
    <style>
        * {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        table td, table th {
            border: 1px solid #ddd;
            padding: 8px;
        }
        table tr:nth-child(even){
            background-color: #f2f2f2;
        }
        table tr:hover{
            background-color: #ddd;
        }
        table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #4CAF50;
            color: white;
        }
    </style>
    <body>
        <?php 
            $path = './' . $_GET["path"];
            $files_and_dirs = scandir($path);

            print('<h2>Directory contents: ' . str_replace('?path=/','',$_SERVER['REQUEST_URI']) . '</h2>');

            // List all files and directories
            print('<table><th>Type</th><th>Name</th><th>Actions</th>');
            foreach ($files_and_dirs as $fnd){
                if ($fnd != ".." and $fnd != ".") {
                    print('<tr>');
                    // ./.git/logs
                    print('<td>' . (is_dir($path . $fnd) ? "Directory" : "File") . '</td>');
                    print('<td>' . (is_dir($path . $fnd) 
                                ? '<a href="' . (isset($_GET['path']) 
                                        ? $_SERVER['REQUEST_URI'] . $fnd . '/' 
                                        : $_SERVER['REQUEST_URI'] . '?path=' . $fnd . '/') . '">' . $fnd . '</a>'
                                : $fnd)
                        . '</td>');
                    print('<td>'
                        . (is_dir($path . $fnd) 
                            ? ''
                            : '<form style="display: inline-block" action="" method="post">
                                <input type="hidden" name="delete" value=' . str_replace(' ', '&nbsp;', $fnd) . '>
                                <input type="submit" value="Delete">
                               </form>'
                        ) 
                        . "</form></td>");
                    print('</tr>');
                }
            }
            print("</table>");
        ?>
        <br><br>
        <nav style="display: inline-block" >
            <button style="display: block; width: 100%"><a href="<?php 
                $q_string = explode('/', rtrim($_SERVER['QUERY_STRING'], '/'));
                array_pop($q_string);
                count($q_string) == 0 
                    ? print('?path=/') 
                    : print('?' . implode('/', $q_string) . '/'); 
                ?>">Back</a>
            </button>
            <br>
            <form action="/FsBrowserPHP" method="get">
                <input type="hidden" name="path" value="<?php print($_GET['path']) ?>" /> 
                <input placeholder="Name of new directory" type="text" id="create_dir" name="create_dir">
                <button type="submit">Submit</button>
            </form>
        </nav>
    </body>
</html>