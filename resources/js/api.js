// var API_URL = process.env.VUE_APP_ENDPOINT + '/api/'
var API_URL = 'http://localhost:8000/api/'

import axios from 'axios'
window.axios = axios

// window.axios.interceptors.request.use(request => {
//     var citizenVal = false;
//     if (request.url.indexOf("project") > -1) {
//         citizenVal = true
//     }
//     if (citizenVal === false && request.url !== API_URL + 'register/spotter' && request.url !== API_URL + 'login/spotter') {
//         const token = localStorage.getItem('isLoggedin');
//         if (token) {
//             request.headers.common['Authorization'] = `Bearer ${token}`
//             request.headers.common['Content-Type'] = 'application/json'
//         }
//     }
//     request.params = request.params || {};
//     request.params['lang'] = String(i18n.locale);
//     return request
// }, function (error) {
//     if (error.response.status === 401) {
//         localStorage.removeItem('userPermissions');
//         localStorage.removeItem('isLoggedin');
//         localStorage.removeItem('email');
//         localStorage.removeItem('password');
//         router.push("/");
//     }
// })

// // Add a 401 response interceptor
// window.axios.interceptors.response.use(function (response) {
//     return response;
// }, function (error) {
//     if (401 === error.response.status) {
//         localStorage.removeItem('userPermissions');
//         localStorage.removeItem('isLoggedin');
//         localStorage.removeItem('email');
//         localStorage.removeItem('password');
//         router.push("/");
//     } else {
//         return Promise.reject(error);
//     }
// });

export const API = {
    getCompanies(data, cb, errorCB) {
        axios.get(API_URL + `companies?page=${data.page}&rowsPerPage=${data.rowsPerPage}`)
        .then(resp => {
            cb(resp.data)
        })
        .catch(err => {
            errorCB(err.response)
        })
    }
}

export default API
