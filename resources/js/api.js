// var API_URL = process.env.VUE_APP_ENDPOINT + '/api/'
var API_URL = 'http://localhost:8000/api/'

import axios from 'axios'
window.axios = axios

export const API = {
    getCompanies(data, cb, errorCB) {
        axios.get(API_URL + `companies?page=${data.page}&rowsPerPage=${data.rowsPerPage}`)
        .then(resp => {
            cb(resp.data)
        })
        .catch(err => {
            errorCB(err.response)
        })
    },
    getCompaniesList(cb, errorCB) {
        axios.get(API_URL + `companiesList`)
        .then(resp => {
            cb(resp.data)
        })
        .catch(err => {
            errorCB(err.response)
        })
    },
    saveCompany(data, cb, errorCB) {
        axios.post(API_URL + `companies`, data, {headers: {"Content-Type": "multipart/form-data"}})
        .then(resp => {
            cb(resp.data)
        })
        .catch(err => {
            errorCB(err.response)
        })
    },
    deleteCompany(data, cb, errorCB) {
        axios.delete(API_URL + `companies/` + data.id)
        .then(resp => {
            cb(resp.data)
        })
        .catch(err => {
            errorCB(err.response)
        })
    },
    updateCompany(data, cb, errorCB) {
        axios.post(API_URL + `companies/` + data.id, data, {headers: {"Content-Type": "multipart/form-data"}})
        .then(resp => {
            cb(resp.data)
        })
        .catch(err => {
            errorCB(err.response)
        })
    },

    // Employees endpoints
    getEmployees(data, cb, errorCB) {
        axios.get(API_URL + `employees?page=${data.page}&rowsPerPage=${data.rowsPerPage}`)
        .then(resp => {
            cb(resp.data)
        })
        .catch(err => {
            errorCB(err.response)
        })
    },
    saveEmployee(data, cb, errorCB) {
        axios.post(API_URL + `employees`, data)
        .then(resp => {
            cb(resp.data)
        })
        .catch(err => {
            errorCB(err.response)
        })
    },
    deleteEmployee(data, cb, errorCB) {
        axios.delete(API_URL + `employees/` + data.id)
        .then(resp => {
            cb(resp.data)
        })
        .catch(err => {
            errorCB(err.response)
        })
    },
    updateEmployee(data, cb, errorCB) {
        axios.put(API_URL + `employees/` + data.id, data)
        .then(resp => {
            cb(resp.data)
        })
        .catch(err => {
            errorCB(err.response)
        })
    }
}

export default API
