<?php
// Include pagination library file 
include_once 'Pagination.class.php';

// Include database configuration file 
require_once 'dbConfig.php';

// Set some useful configuration 
$baseURL = 'getData.php';
$limit = 5;

// Count of all records 
$query   = $db->query("SELECT COUNT(*) as rowNum FROM users");
$result  = $query->fetch_assoc();
$rowCount = $result['rowNum'];

// Initialize pagination class 
$pagConfig = array(
    'baseURL' => $baseURL,
    'totalRows' => $rowCount,
    'perPage' => $limit,
    'contentDiv' => 'dataContainer',
    'link_func' => 'searchFilter'
);
$pagination =  new Pagination($pagConfig);

// Fetch records based on the limit 
$query = $db->query("SELECT * FROM users ORDER BY id DESC LIMIT $limit");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJAX Pagination</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        function searchFilter(page_num) {
            page_num = page_num ? page_num : 0;
            var keywords = $('#keywords').val();
            var filterBy = $('#filterBy').val();
            $.ajax({
                type: 'POST',
                url: 'getData.php',
                data: 'page=' + page_num + '&keywords=' + keywords + '&filterBy=' + filterBy,
                beforeSend: function() {
                    $('.loading-overlay').show();
                },
                success: function(html) {
                    $('#dataContainer').html(html);
                    $('.loading-overlay').fadeOut("slow");
                }
            });
        }
    </script>
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-5 text-center">Ajax Pagination with Search and Filter in PHP</h1>

        <div class="search-panel mb-4">
            <div class="row justify-content-end">
                <div class="form-group col-4">
                    <input type="text" class="form-control" id="keywords" placeholder="Type keywords..." onkeyup="searchFilter();">
                </div>
                <div class="form-group col-2">
                    <select class="form-control" id="filterBy" onchange="searchFilter();">
                        <option value="">Filter by Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="datalist-wrapper">

            <!-- Data list container -->
            <div id="dataContainer">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">First Name</th>
                            <th scope="col">Last Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Country</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($query->num_rows > 0) {
                            $i = 0;
                            while ($row = $query->fetch_assoc()) {
                                $i++;
                        ?>
                                <tr>
                                    <th scope="row"><?php echo $i; ?></th>
                                    <td><?php echo $row["first_name"]; ?></td>
                                    <td><?php echo $row["last_name"]; ?></td>
                                    <td><?php echo $row["email"]; ?></td>
                                    <td><?php echo $row["country"]; ?></td>
                                    <td><?php echo ($row["status"] == 1) ? 'Active' : 'Inactive'; ?></td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo '<tr><td colspan="6">No records found...</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Display pagination links -->
                <?php echo $pagination->createLinks(); ?>
            </div>
        </div>
    </div>
</body>

</html>