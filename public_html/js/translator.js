var host = "http://zk.horizon2.pro-linuxpl.com";
var baseurl = "";
var translator = {
  input: {},
  output: {},
  run: function(input) {
    var $t = this;
    $t.input = input;
    $.ajax({
      async: false,
      url: baseurl + "/admin/validator/translate",
      type: "post",
      dataType: "json",
      data: $t.input,
      success: function(resp) {
        if (resp)
          $t.output = resp;
      }
    });
  },
  get: function(name) {
    if (Object.keys(this.output).length)
      return this.output.text[name];
    return "";
  },
  get_all: function() {
    if (Object.keys(this.output).length)
      return this.output.text;
    return "";
  }
};
var input = {};
input.text = {};
input.text.btn_anuluj = "Anuluj";
input.text.btn_dodaj = "Dodaj";
input.text.btn_edytuj = "Edytuj";
input.text.btn_filtruj = "Filtruj";
input.text.btn_nastepny = "Następny";
input.text.btn_poprzedni = "Poprzedni";
input.text.btn_resetuj = "Resetuj";
input.text.btn_pokaz = "Pokaż";
input.text.btn_ukryj = "Ukryj";
input.text.btn_pokaz_view = "pokaż";
input.text.btn_ukryj_view = "ukryj";
input.text.btn_szczegoly = "Szczegóły";
input.text.btn_zamknij = "Zamknij";
input.text.btn_zapisz = "Zapisz";
input.text.btn_usun = "Usuń zaznaczone";
input.text.btn_jako_odebrany = "Ustaw jako odebrany";
input.text.btn_jako_nieodebrany = "Ustaw jako nieodebrany";
input.text.btn_zaplac = "Zapłać";
input.text.btn_zmien_termin = "Edycja terminu";
input.text.btn_zloz_nowe_zamowienie = "Złóż nowe zamówienie";

input.text.czas_calkowity = "Czas całkowity:";
input.text.data_rozpoczecia = "Data rozpoczęcia:";
input.text.data_zakonczenia = "Data zakończenia:";
input.text.kategoria_pytania = "Kategoria pytania:";
input.text.komentarz = "Komentarz:";
input.text.temat = "Temat:";
input.text.oczekujace_rozmowy = " - Masz oczekujące rozmowy";
input.text.oczekujace_zgloszenia = " - Masz nowe zgłoszenie";
input.text.chat_zakonczony = "Rozmowa została zakończona";
input.text.pisze = "pisze";
input.text.wybierz = "Wybierz";
input.text.brak_wynikow = "Brak wyników dla ustalonego kryterium filtrowania";

input.text.dialog_blad = "Błąd";
input.text.dialog_potwierdzenie = "Potwierdzenie";
input.text.dialog_niepowodzenie = "Niepowodzenie";
input.text.dialog_dodaj_zdjecie = "Dodaj zdjęcie";
input.text.dialog_edytuj_zdjecie = "Edytuj zdjęcie";
input.text.dialog_dodaj_plik = "Dodaj plik";
input.text.dialog_usuniecie_zdjecia = "Czy na pewno chcesz usunąć wybrane zdjęcie(a)?";
input.text.dialog_usuniecie_zgloszenia = "Czy na pewno chcesz usunąć wybrane zgłoszenie(a)?";
input.text.dialog_usuniecie_pytania_faq = "Czy na pewno chcesz usunąć wybrany pytanie z bazy FAQ?";
input.text.dialog_usuniecie_kategorii = "Czy na pewno chcesz usunąć wybraną wybraną kategorię";
input.text.dialog_usuniecie_adresu_url = "Czy na pewno chcesz usunąć wybrany url z bazy adresów?";
input.text.dialog_usuniecie_aktualnosci = "Czy na pewno chcesz usunąć wybraną aktualność?";
input.text.dialog_usuniecie_potrfolio = "Czy na pewno chcesz usunąć wybrane portfolio?";
input.text.dialog_usuniecie_powiadomienie_email = "Czy na pewno chcesz usunąć powiadomienie e-mail?";
input.text.dialog_usuniecie_uzytkownika = "Czy na pewno chcesz usunąć wybranego użytkownika?";
input.text.dialog_usuniecie_grupy = "Czy na pewno chcesz usunąć wybraną grupę użytkowników?";
input.text.dialog_usuniecie_galerii = "Czy na pewno chcesz usunąć wybraną galerię?";
input.text.dialog_usuniecie_slidera = "Czy na pewno chcesz usunąć wybrany slider?";
input.text.dialog_usuniecie_elementu = "Czy na pewno chcesz usunąć wybrany element?";
input.text.dialog_usuniecie_filmu = "Czy na pewno chcesz usunąć wybrany film?";
input.text.dialog_usuniecie_slowa_kluczowego = "Czy na pewno chcesz usunąć wybrane słowo kluczowe?";
input.text.dialog_jako_odebrany = "Czy na pewno chcesz ustawić status listu jako 'odebrany'?";
input.text.dialog_jako_nieodebrany = "Czy na pewno chcesz ustawić status listu jako 'nieodebrany'?";
input.text.dialog_wykonanie_akcji = "Czy na pewno chcesz wykonać akcję:";
input.text.dialog_potwierdzenie = "Potwierdzenie";
input.text.dialog_sukces = "Sukces";
input.text.dialog_sukces_operacja = "Operacja zakończona sukcesem";
input.text.dialog_sukces_aktualizacja_konta = "Twoje konto zostało zaktualizowane";
input.text.dialog_sukces_utworzenie_konta = "Twoje konto zostało utworzone, ale nie zostało jeszcze potwierdzone. Sprawdź swoje konto e-mail, aby dowiedzieć się wiecej";
input.text.dialog_sukces_przypomnienie_hasla = "Nowe hasło zostało wysłane na twój adres e-mail";
input.text.dialog_wystapil_blad = "Wystąpił błąd";
input.text.dialog_wiadomosc_email_wyslana = "Wiadomość e-mail została wysłana";
input.text.dialog_zgloszenie_helpdesk_utworzone = "Zgłoszenie helpdesk zostało utworzone";
input.text.dialog_zgloszenie = "Zgłoszenie";
input.text.dialog_brak_wynikow_dla_kryterium = "Brak wyników dla ustalonego kryterium filtrowania";
input.text.dialog_brak_wynikow = "Brak wyników";
input.text.dialog_zamowienie_zostalo_przekazane_do_realizacji = "Twoje zamówienie zostało przekazane do realizacji. O kolejnej zmianie statusu zostaniesz poinformowany drogą mailową";
input.text.dialog_zamowienie_zostalo_utworzone_i_wyslane_do_wyceny = "Twoje zamówienie zostało utworzone i wysłane do wyceny. O kolejnej zmianie statusu zostaniesz poinformowany drogą mailową";
input.text.dialog_zamowienie_nie_moze_zostac_zrealizowane = "Twoje zamówienie nie może zostać zrealizowane. Zamówienie zostało przeniesione do zakładki Archiwum";
input.text.dialog_wykonanie_akcji = "Czy na pewno chcesz wykonać akcję:";
input.text.dialog_faktura = "Faktura zostanie wysłana pocztą pod wskazany adres w ciągu 7 dni roboczych";
translator.run(input);
