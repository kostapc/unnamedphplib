functions list
merge arrays in templates

    buildFormattedCycledArray($inArray, $template, $from_variable = false) process array to template file or template in string. template loks like: ``` header
    begin

{column1}{column2}{column3}
end

footer ``` * buildFluidFormattedCycledArray($inArray, $template, $from_variable = false, $inHeadRow = null) like previous function, but without fixed column names. see sources - description coming soon... * justTemplate($template) - get template file * buildFormattedFlatdArray($inArray, $template) - if query contents only one row, merging array in template for just one row.
get usefull arrays from database

    buildSingleRowArray($in_query, $assoc = true) - get just first row from db
    createFetchedArray($query, $assoc = false, &$column_names = null) -
    createFlatArray($query) - get arrays of arrays of rows values.

    getFirstOrNone($query) - get just one (first column, first row) element from db.
    generate queries from arrays

    createQueryFromRightArray($inArray, $table) - create insert query for array that has keys similiar to mysql table column names.
    createUpdateQueryFromRightArray($inArray, $table, $keyArray = null, $justAndValues = null) - like previous, but for update query.

    create_delete_query($table, $columns, $values) - ...and delete query.
    just shells for mysql_execute

    doMQuery($in_query) - with exception

    doMQueryClr($in_query) - with error string return if fails
    process arrays for mysql

    createStringFromArray($inArray, $btw) - implode
    prepareArray($inArray) - escape all values

    prepareArrayFromPOST($inArray) - escape and htmlspecialchars
    put array in database

    insertRightArray($table, $rightArray) - insert escaped array
    no comments..

    pr($in_array) - print_r
    pt($in_string) - textarea

    dp($str) - hightlight out
    rape strings

    cut_center_of_string($input, $cut_lenght = 35)
    cut_first_char($str)
    get_first_char($str)
    cut_some_first_chars($str, $chars)

    cut_last_chars($str, $chars)
    rape file names

    get_file_extension($file_name)
    get_file_name($full_file_path)

    get_file_directory($file_name)
    rape URL's

    getHOSTURL($inString)
    getROOTURL($inString)
    getJUSTURL($inString)
    isValidUrl($str)

    isValidEmail($email)
    misc

    build_array_for_table($table_name)
    list_php_server_variable()
