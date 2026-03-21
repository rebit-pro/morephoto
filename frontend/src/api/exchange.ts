import api from './http';

export interface OrderBookEntry {
  id: string;
  side: 'buy' | 'sell';
  price: string;
  amount: string;
  minLimit: string;
  maxLimit: string;
  username: string;
  completedTrades: number;
  completionRate: number;
  paymentMethods: string[];
}

export interface CurrencyPair {
  token: string;
  fiat: string;
  label: string;
}

export interface PaymentMethod {
  id: string;
  name: string;
  icon?: string;
}

export interface OrderBookResponse {
  buy: OrderBookEntry[];
  sell: OrderBookEntry[];
}

export const exchangeApi = {
  getOrderBook(token: string, fiat: string): Promise<OrderBookResponse> {
    return api.get('/api/v1/exchange/orderbook', { params: { token, fiat } }).then((r) => r.data);
  },

  getCurrencyPairs(): Promise<CurrencyPair[]> {
    return api.get('/api/v1/exchange/currency-pairs').then((r) => r.data);
  },

  getPaymentMethods(): Promise<PaymentMethod[]> {
    return api.get('/api/v1/exchange/payment-methods').then((r) => r.data);
  }
};
