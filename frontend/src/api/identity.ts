import api from './http';

export interface ApiConnectionRequest {
  apiKey: string;
  secretKey: string;
  mode: 'testnet' | 'mainnet';
}

export interface ApiConnectionStatus {
  connected: boolean;
  mode: 'testnet' | 'mainnet' | null;
  status: 'active' | 'invalid' | 'revoked' | 'pending_verification' | null;
}

export const identityApi = {
  connect(data: ApiConnectionRequest): Promise<ApiConnectionStatus> {
    return api.post('/api/v1/identity/connection', data).then((r) => r.data);
  },

  disconnect(): Promise<void> {
    return api.delete('/api/v1/identity/connection');
  },

  verify(): Promise<ApiConnectionStatus> {
    return api.post('/api/v1/identity/connection/verify').then((r) => r.data);
  },

  status(): Promise<ApiConnectionStatus> {
    return api.get('/api/v1/identity/connection/status').then((r) => r.data);
  }
};
