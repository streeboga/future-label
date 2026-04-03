import { api } from '@/lib/api';
import type {
  ForgotPasswordRequest,
  JsonApiUser,
  LoginRequest,
  RegisterRequest,
  ResetPasswordRequest,
  User,
} from '@/types/auth';

export async function login(data: LoginRequest): Promise<{ token: string; user: User }> {
  const response = await api.post('/auth/login', data);
  const { id, attributes } = response.data.data;
  const token = response.data.meta.token;
  return {
    token,
    user: {
      id,
      name: attributes.name,
      email: attributes.email,
      role: attributes.role,
    },
  };
}

export async function register(data: RegisterRequest): Promise<void> {
  await api.post('/auth/register', data);
}

export async function forgotPassword(data: ForgotPasswordRequest): Promise<void> {
  await api.post('/auth/forgot-password', data);
}

export async function resetPassword(data: ResetPasswordRequest): Promise<void> {
  await api.post('/auth/reset-password', data);
}

export async function fetchCurrentUser(): Promise<User> {
  const response = await api.get<JsonApiUser>('/me');
  const { id, attributes } = response.data.data;
  return {
    id,
    name: attributes.name,
    email: attributes.email,
    role: attributes.role,
  };
}

export async function logout(): Promise<void> {
  try {
    await api.post('/auth/logout');
  } finally {
    localStorage.removeItem('auth_token');
  }
}
