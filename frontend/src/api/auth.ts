import api from './http';

export interface GeeTestCaptchaPayload {
  lot_number: string;
  captcha_output: string;
  pass_token: string;
  gen_time: string;
}

export interface LoginRequest {
  email: string;
  password: string;
  captcha?: GeeTestCaptchaPayload;
}

export interface RegisterRequest {
  email: string;
  password: string;
}

export interface AuthUser {
  id: number;
  email: string;
  name: string;
}

export interface LoginResponse {
  token: string;
  user: AuthUser;
}

export const authApi = {
  login(data: LoginRequest): Promise<LoginResponse> {
    return api.post('/api/v1/auth/login', data).then((r) => r.data);
  },

  register(data: RegisterRequest): Promise<LoginResponse> {
    return api.post('/api/v1/auth/register', data).then((r) => r.data);
  },

  logout(): Promise<void> {
    return api.post('/api/v1/auth/logout');
  }
};
