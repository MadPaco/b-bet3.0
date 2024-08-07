// src/utility/axiosInstance.ts
import axios from 'axios';
import { jwtDecode, JwtPayload as DefaultJwtPayload } from 'jwt-decode';
import { fetchNewToken } from './api';

interface JwtPayload extends DefaultJwtPayload {
    username: string;
    roles: string[];
}

const BASE_URL = 'http://127.0.0.1:8000/api';
const TOKEN_REFRESH_THRESHOLD = 60; // 60 seconds

let isRefreshing = false;
let refreshSubscribers: ((token: string) => void)[] = [];

const api = axios.create({
    baseURL: BASE_URL,
});

const onRefreshed = (token: string) => {
    refreshSubscribers.forEach((callback) => callback(token));
    refreshSubscribers = [];
};

const addRefreshSubscriber = (callback: (token: string) => void) => {
    refreshSubscribers.push(callback);
};

api.interceptors.request.use(
    async (config) => {
        const token = localStorage.getItem('token');
        if (token) {
            const decoded = jwtDecode<JwtPayload>(token);
            const current_time = Date.now().valueOf() / 1000;

            if (decoded.exp && decoded.exp < current_time + TOKEN_REFRESH_THRESHOLD) {
                if (!isRefreshing) {
                    isRefreshing = true;
                    try {
                        await fetchNewToken();
                        const newToken = localStorage.getItem('token');
                        if (newToken) {
                            onRefreshed(newToken);
                        }
                    } catch (error) {
                        console.error('Token refresh failed:', error);
                        localStorage.removeItem('token');
                        localStorage.removeItem('refresh_token');
                        throw error;
                    } finally {
                        isRefreshing = false;
                    }
                }

                return new Promise((resolve) => {
                    addRefreshSubscriber((newToken) => {
                        if (config.headers) {
                            config.headers.Authorization = `Bearer ${newToken}`;
                        }
                        resolve(config);
                    });
                });
            } else {
                if (config.headers) {
                    config.headers.Authorization = `Bearer ${token}`;
                }
            }
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

api.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;

        if (error.response.status === 401 && !originalRequest._retry) {
            originalRequest._retry = true;
            try {
                await fetchNewToken();
                const newToken = localStorage.getItem('token');
                if (newToken) {
                    originalRequest.headers.Authorization = `Bearer ${newToken}`;
                    return api(originalRequest);
                }
            } catch (err) {
                console.error('Token refresh failed:', err);
            }
        }
        return Promise.reject(error);
    }
);

export default api;
