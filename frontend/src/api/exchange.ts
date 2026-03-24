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

interface CurrencyPairDto {
  id: number;
  code: string;
  tokenCurrencyId: number;
  fiatCurrencyId: number;
  tokenCode: string;
  fiatCode: string;
  isDefault: boolean;
  sort: number;
}

interface PaymentMethodDto {
  id: number;
  code: string;
  name: string;
  sort: number;
}

export const exchangeApi = {
  getOrderBook(token: string, fiat: string): Promise<OrderBookResponse> {
    return api.get('/api/v1/exchange/orderbook', { params: { token, fiat } }).then((r) => r.data);
  },

  getCurrencyPairs(): Promise<CurrencyPair[]> {
    return api.get('/api/v1/exchange/currency-pairs').then((r) => {
      const items: CurrencyPairDto[] = r.data?.items ?? [];
      return items.map((item) => ({
        token: item.tokenCode,
        fiat: item.fiatCode,
        label: `${item.tokenCode} / ${item.fiatCode}`,
      }));
    });
  },

  getPaymentMethods(): Promise<PaymentMethod[]> {
    return api.get('/api/v1/exchange/payment-methods').then((r) => {
      const items: PaymentMethodDto[] = r.data?.items ?? [];
      return items.map((item) => ({
        id: String(item.id),
        name: item.name,
      }));
    });
  }
};
