import api from './http';

export interface Balance {
  id: number;
  userId: number;
  currencyId: number;
  currency: string;
  available: string;
  locked: string;
  total: string;
  syncedAt: string | null;
}

export interface Transaction {
  id: string;
  type: 'deposit' | 'withdrawal' | 'trade_buy' | 'trade_sell' | 'lock' | 'unlock' | 'fee';
  amount: string;
  currency: string;
  tradeId: string | null;
  createdAt: string;
}

export interface TransactionListResponse {
  transactions: Transaction[];
  total: number;
}

export interface TransactionFilters {
  type?: string;
  currencyId?: number;
  dateFrom?: string;
  dateTo?: string;
  limit?: number;
  offset?: number;
}

export const walletApi = {
  getBalances(): Promise<Balance[]> {
    return api.get('/api/v1/wallet/balances').then((r) => r.data?.balances ?? r.data);
  },

  syncBalances(): Promise<Balance[]> {
    return api.post('/api/v1/wallet/balances/sync').then((r) => r.data?.balances ?? r.data);
  },

  getTransactions(params?: TransactionFilters): Promise<TransactionListResponse> {
    return api.get('/api/v1/wallet/transactions', { params }).then((r) => ({
      transactions: r.data?.transactions ?? r.data ?? [],
      total: r.data?.total ?? 0,
    }));
  },

  exportTransactions(params?: Omit<TransactionFilters, 'limit' | 'offset'>): Promise<Blob> {
    return api
      .get('/api/v1/wallet/transactions/export', { params, responseType: 'blob' })
      .then((r) => r.data);
  },
};
