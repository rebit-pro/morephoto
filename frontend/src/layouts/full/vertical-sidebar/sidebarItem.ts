import {
  DashboardIcon,
  ArrowsExchangeIcon,
  WalletIcon,
  UserCircleIcon,
  HistoryIcon,
  PlugConnectedIcon,
  ReportMoneyIcon
} from 'vue-tabler-icons';

export interface menu {
  id?: string;
  header?: string;
  title?: string;
  icon?: object;
  to?: string;
  getURL?: boolean;
  divider?: boolean;
  chip?: string;
  chipColor?: string;
  chipVariant?: string;
  chipIcon?: string;
  children?: menu[];
  disabled?: boolean;
  type?: string;
  subCaption?: string;
}

const sidebarItem: menu[] = [
  { header: 'Главная' },
  {
    id: 'dashboard',
    title: 'Дашборд',
    icon: DashboardIcon,
    to: '/dashboard'
  },
  {
    id: 'orderbook',
    title: 'P2P Стакан',
    icon: ArrowsExchangeIcon,
    to: '/orderbook'
  },

  { divider: true },
  { header: 'Кошелёк' },
  {
    id: 'balances',
    title: 'Балансы',
    icon: WalletIcon,
    to: '/wallet/balances'
  },
  {
    id: 'transactions',
    title: 'Транзакции',
    icon: HistoryIcon,
    to: '/wallet/transactions'
  },

  { divider: true },
  { header: 'Профиль' },
  {
    id: 'profile',
    title: 'Мой профиль',
    icon: UserCircleIcon,
    to: '/profile'
  },
  {
    id: 'api-connection',
    title: 'Подключение Bybit',
    icon: PlugConnectedIcon,
    to: '/profile/api-connection'
  }
];

export default sidebarItem;
