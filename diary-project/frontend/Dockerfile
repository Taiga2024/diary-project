From node:latest

WORKDIR /usr/src/app

COPY package*.json ./

RUN npm install

COPY . .

CMD ["node", ".output/server/index.mjs"]

EXPOSE 3000