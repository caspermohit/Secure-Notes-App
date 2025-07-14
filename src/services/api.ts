import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Request interceptor to add auth token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor to handle auth errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export const authAPI = {
  register: (data: { name: string; email: string; password: string; password_confirmation: string }) =>
    api.post('/register', data),
  
  login: (data: { email: string; password: string }) =>
    api.post('/login', data),
  
  logout: () => api.post('/logout'),
  
  getUser: () => api.get('/user'),
};

export const notesAPI = {
  getNotes: (params?: any) => api.get('/notes', { params }),
  
  getNote: (id: number) => api.get(`/notes/${id}`),
  
  createNote: (data: any) => api.post('/notes', data),
  
  updateNote: (id: number, data: any) => api.put(`/notes/${id}`, data),
  
  deleteNote: (id: number) => api.delete(`/notes/${id}`),
  
  toggleArchive: (id: number) => api.patch(`/notes/${id}/archive`),
  
  togglePin: (id: number) => api.patch(`/notes/${id}/pin`),
};

export const tagsAPI = {
  getTags: () => api.get('/tags'),
  
  createTag: (data: { name: string; color?: string }) => api.post('/tags', data),
  
  updateTag: (id: number, data: { name?: string; color?: string }) => api.put(`/tags/${id}`, data),
  
  deleteTag: (id: number) => api.delete(`/tags/${id}`),
};

export default api; 