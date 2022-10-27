import { faker } from "https://cdn.skypack.dev/@faker-js/faker";

function getData() {
  const data = {
    imie: faker.name.firstName(),
    nazwisko: faker.name.lastName(),
    login: [
      faker.name.firstName() + Math.floor(Math.random() * 100),
      faker.name.firstName() + Math.floor(Math.random() * 100),
      faker.name.firstName() + Math.floor(Math.random() * 100),
    ],
    haslo: [
      faker.internet.password(),
      faker.internet.password(),
      faker.internet.password(),
    ],
    adres: faker.address.street() + " " + Math.floor(Math.random() * 100),
    miasto: faker.address.city(),
    wojewodztwo: faker.address.state(),
    telefon: faker.phone.number("#########"),
    kod_pocztowy: faker.address.zipCode("##-###"),
    email: faker.internet.email(),
    nazwa: faker.lorem.words(1),
    isbn: faker.random.numeric(11),
    tytul: faker.random.words(),
    autor: faker.name.fullName(),
    stron: Math.floor(Math.random() * 1000) + 80,
    wydawnictwo: faker.random.words(),
    rok_wydania: Math.floor(Math.random() * 122) + 1900,
    opis: faker.lorem.sentences(),
    data_zamowienia: faker.date.between(subtractMonths(8), subtractMonths(6)),
    data_odbioru: faker.date.between(subtractMonths(5), subtractMonths(3)),
    data_zwrotu: faker.date.between(subtractMonths(2), subtractMonths(0)),
    cena: Math.floor(Math.random() * 90) + 10,
  };

  return data;
}

const data = getData();
document.cookie = `data = ${JSON.stringify(data)}`;

function subtractMonths(numOfMonths, date = new Date()) {
  date.setMonth(date.getMonth() - numOfMonths);
  return date.toISOString();
}
