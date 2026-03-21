import ru from './ru.json';

export const messages = {
  ru: {
    ...ru,
    $vuetify: {
      ...ru,
      open: 'Открыть',
      close: 'Закрыть',
      input: {
        clear: 'Очистить',
        appendAction: '',
        prependAction: '',
        otp: 'Введите символ {0}'
      },
      fileInput: {
        counter: '{0} файлов',
        counterSize: '{0} файлов'
      },
      confirmEdit: {
        ok: 'ОК',
        cancel: 'Отмена'
      },
      carousel: {
        ariaLabel: {
          delimiter: 'Слайд {0} из {1}'
        }
      },
      rating: {
        ariaLabel: {
          item: 'Рейтинг {0} из {1}'
        }
      },
      dataTable: {
        noDataText: 'Нет данных',
        itemsPerPageText: 'Элементов на странице:',
        sortBy: 'Сортировать по'
      },
      noDataText: 'Нет данных',
      dataFooter: {
        firstPage: 'Первая страница',
        prevPage: 'Предыдущая',
        nextPage: 'Следующая',
        lastPage: 'Последняя страница',
        itemsPerPageText: 'Элементов на странице:',
        itemsPerPageAll: 'Все',
        pageText: '{0}-{1} из {2}'
      }
    }
  }
};
