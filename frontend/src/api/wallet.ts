import api from './http';

export interface Balance {
  currency: string;
  available: string;
  locked: string;
  total: string;
}

export interface Transaction {
  id: string;
  type: 'deposit' | 'withdrawal' | 'trade_buy' | 'trade_sell' | 'lock' | 'unlock' | 'fee';
  amount: string;
  currency: string;
  tradeId: string | null;
  createdAt: string;
}

export const walletApi = {
  getBalances(): Promise<Balance[]> {
    return api.get('/api/v1/wallet/balances').then((r) => r.data);
  },

  getTransactions(params?: { page?: number; perPage?: number; type?: string }): Promise<Transaction[]> {
    return api.get('/api/v1/wallet/transactions', { params }).then((r) => r.data);
  }
};
