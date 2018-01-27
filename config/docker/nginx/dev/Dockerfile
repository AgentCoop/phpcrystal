FROM nginx:1.13.1

COPY ./sites-enabled/app.conf /etc/nginx/conf.d/lpb.com.conf
COPY nginx.conf /etc/nginx/nginx.conf

COPY ./self-signed-key/server.key /etc/ssl/certs/lpb.key
COPY ./self-signed-key/server.crt /etc/ssl/certs/lpb.crt
COPY ./dhparam.pem /etc/ssl/certs/dhparam.pem
COPY ./ssl-params.conf /etc/nginx/

RUN rm -f /etc/nginx/conf.d/default.conf

COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]
