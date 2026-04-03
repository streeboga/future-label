export interface User {
  id: string;
  name: string;
  email: string;
  role: 'artist' | 'manager' | 'admin';
}

export interface LoginRequest {
  email: string;
  password: string;
}

export interface RegisterRequest {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}

export interface ForgotPasswordRequest {
  email: string;
}

export interface ResetPasswordRequest {
  email: string;
  password: string;
  password_confirmation: string;
  token: string;
}

export interface AuthResponse {
  data: {
    token: string;
    user: User;
  };
}

export interface JsonApiUser {
  data: {
    id: string;
    type: string;
    attributes: {
      name: string;
      email: string;
      role: 'artist' | 'manager' | 'admin';
    };
  };
}
