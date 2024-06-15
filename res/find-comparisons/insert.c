zval function_name;
zval my_retval;
ZVAL_STRING(&function_name, "debug_print_backtrace2");

if (SUCCESS == call_user_function(NULL, NULL, &function_name, &my_retval, 0, NULL)) {
    printf("Function call failed\n");
}

zval_ptr_dtor(&function_name);
    printf("Function call failed\n");
// zval_ptr_dtor(&my_retval);
/*

// call_user_function(function_table, object, function_name, retval_ptr, param_count, params) \ 
// call_user_function(NULL, NULL, &function_name, &retval, 0, NULL);

zval_ptr_dtor(&function_name);
zval_ptr_dtor(&retval);
*/