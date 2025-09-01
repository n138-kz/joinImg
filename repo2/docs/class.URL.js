class URL {
    getQuery () {
        let query_list = get_GETarray((decodeURI(location.search)+'&').replace(/^\?/,''));
        query_list.code = ( query_list.code === "" || query_list.code === null || query_list.code === undefined )?'':query_list.code;
        query_list.access_token = ( query_list.access_token === "" || query_list.access_token === null || query_list.access_token === undefined )?'':query_list.access_token;
        query_list.error = ( query_list.error === "" || query_list.error === null || query_list.error === undefined )?'':query_list.error;
        query_list.error_description = ( query_list.error_description === "" || query_list.error_description === null || query_list.error_description === undefined )?'':decodeURIComponent(query_list.error_description.replace(/\+/g,'%20'));
        return query_list;
    }
}
console.log('Loaded class.URL.js');
