import api from './http';

// region Interfaces — Order Book

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

export interface OrderBookResponse {
  buy: OrderBookEntry[];
  sell: OrderBookEntry[];
}

// endregion

// region Interfaces — Dictionaries

export interface Currency {
  id: number;
  code: string;
  name: string;
  type: 'crypto' | 'fiat';
  decimals: number;
  sort: number;
}

export interface CurrencyPair {
  id: number;
  token: string;
  fiat: string;
  label: string;
}

export interface PaymentMethod {
  id: string;
  code: string;
  name: string;
  sort: number;
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

// endregion

// region Interfaces — Advertisements

export type AdvertisementStatus = 'active' | 'paused' | 'completed' | 'cancelled';
export type AdvertisementSide = 'buy' | 'sell';
export type PriceType = 'fixed' | 'floating';

export interface Advertisement {
  id: number;
  currencyPairId: number;
  side: AdvertisementSide;
  priceType: PriceType;
  price: string;
  premium: string | null;
  quantity: string;
  minAmount: string;
  maxAmount: string;
  paymentMethodIds: string[];
  paymentPeriod: number;
  conditions: string;
  chatScriptId: number | null;
  status: AdvertisementStatus;
  createdAt: string;
  updatedAt: string;
}

export interface CreateAdvertisementPayload {
  currencyPairId: number;
  side: AdvertisementSide;
  priceType: PriceType;
  price: string;
  premium: string | null;
  quantity: string;
  minAmount: string;
  maxAmount: string;
  paymentMethodIds: string[];
  paymentPeriod: number;
  conditions: string;
  chatScriptId: number | null;
  tradingPreferenceSet: Record<string, unknown>;
}

// endregion

// region Interfaces — Trades

export type TradeStatus = 'pending_payment' | 'payment_sent' | 'payment_confirmed' | 'completed' | 'cancelled' | 'disputed';

export interface Trade {
  id: number;
  bybitOrderId: string;
  bybitStatus: number;
  side: 'buy' | 'sell';
  price: number;
  quantity: number;
  fiatAmount: number;
  fee: number;
  status: TradeStatus;
  counterpartyName: string;
  currencyPairId: number;
  advertisementId: number | null;
  paymentDeadline: string | null;
  paidAt: string | null;
  completedAt: string | null;
  cancelledAt: string | null;
  cancelReason: string | null;
  createdAt: string;
  updatedAt: string;
}

export interface ConfirmPaymentPayload {
  paymentType: string;
  paymentId: string;
}

// endregion

// region Interfaces — Chat

export type ChatContentType = 'str' | 'pic' | 'pdf' | 'video';

export interface ChatMessage {
  id: number;
  tradeId: number;
  senderType: 'user' | 'system' | 'script';
  message: string;
  contentType: ChatContentType;
  fileName: string | null;
  createdAt: string;
}

export interface SendMessagePayload {
  tradeId: number;
  message: string;
  contentType: ChatContentType;
  fileName: string | null;
}

// endregion

// region Interfaces — Chat Scripts

export interface ChatScriptStep {
  sort: number;
  message: string;
  delaySeconds: number;
}

export interface ChatScript {
  id: number;
  name: string;
  isActive: boolean;
  steps: ChatScriptStep[];
  advertisementsCount?: number;
  createdAt: string;
  updatedAt: string;
}

export interface ChatScriptPayload {
  name: string;
  isActive: boolean;
  steps: ChatScriptStep[];
}

// endregion

// region API

export const exchangeApi = {
  // — Dictionaries —

  getCurrencies(): Promise<Currency[]> {
    return api.get('/api/v1/exchange/currencies').then((r) => r.data?.items ?? []);
  },

  getCurrencyPairs(): Promise<CurrencyPair[]> {
    return api.get('/api/v1/exchange/currency-pairs').then((r) => {
      const items: CurrencyPairDto[] = r.data?.items ?? [];
      return items.map((item) => ({
        id: item.id,
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
        code: item.code,
        name: item.name,
        sort: item.sort,
      }));
    });
  },

  getOrderBook(token: string, fiat: string): Promise<OrderBookResponse> {
    return api.get('/api/v1/exchange/orderbook', { params: { token, fiat } }).then((r) => r.data);
  },

  // — Advertisements —

  getAdvertisements(status?: AdvertisementStatus): Promise<Advertisement[]> {
    return api
      .get('/api/v1/exchange/advertisements', { params: status ? { status } : {} })
      .then((r) => r.data?.items ?? r.data ?? []);
  },

  createAdvertisement(payload: CreateAdvertisementPayload): Promise<Advertisement> {
    return api.post('/api/v1/exchange/advertisements', payload).then((r) => r.data);
  },

  deleteAdvertisement(id: number): Promise<void> {
    return api.delete(`/api/v1/exchange/advertisements/${id}`);
  },

  toggleAdvertisement(id: number, status: 'active' | 'paused'): Promise<Advertisement> {
    return api.patch(`/api/v1/exchange/advertisements/${id}`, { status }).then((r) => r.data);
  },

  // — Trades —

  getTrades(status?: TradeStatus): Promise<Trade[]> {
    return api
      .get('/api/v1/exchange/trades', { params: status ? { status } : {} })
      .then((r) => r.data?.items ?? r.data ?? []);
  },

  getTradeDetail(id: number): Promise<Trade> {
    return api.get(`/api/v1/exchange/trades/${id}`).then((r) => r.data);
  },

  confirmPayment(id: number, payload: ConfirmPaymentPayload): Promise<Trade> {
    return api.post(`/api/v1/exchange/trades/${id}/pay`, payload).then((r) => r.data);
  },

  releaseAssets(id: number): Promise<Trade> {
    return api.post(`/api/v1/exchange/trades/${id}/release`).then((r) => r.data);
  },

  // — Trade Chat —

  getChatHistory(tradeId: number): Promise<ChatMessage[]> {
    return api
      .get(`/api/v1/exchange/trades/${tradeId}/chat`)
      .then((r) => r.data?.messages ?? r.data ?? []);
  },

  sendMessage(tradeId: number, payload: SendMessagePayload): Promise<ChatMessage> {
    return api.post(`/api/v1/exchange/trades/${tradeId}/chat`, payload).then((r) => r.data);
  },

  // — Chat Scripts —

  getChatScripts(): Promise<ChatScript[]> {
    return api.get('/api/v1/exchange/chat-scripts').then((r) => r.data?.items ?? r.data ?? []);
  },

  createChatScript(payload: ChatScriptPayload): Promise<ChatScript> {
    return api.post('/api/v1/exchange/chat-scripts', payload).then((r) => r.data);
  },

  updateChatScript(id: number, payload: ChatScriptPayload): Promise<ChatScript> {
    return api.patch(`/api/v1/exchange/chat-scripts/${id}`, payload).then((r) => r.data);
  },

  deleteChatScript(id: number): Promise<void> {
    return api.delete(`/api/v1/exchange/chat-scripts/${id}`);
  },
};

// endregion
