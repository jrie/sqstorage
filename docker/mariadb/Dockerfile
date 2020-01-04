# vim:set ft=dockerfile:
FROM alpine:latest

# add our user and group first to make sure their IDs get assigned consistently, regardless of whatever dependencies get added
RUN addgroup -S mysql && adduser -S -G mysql mysql

# add gosu for easy step-down from root
RUN set -ex; \
	\
    apk --update --no-cache add -X http://dl-cdn.alpinelinux.org/alpine/edge/testing \
    gosu; \
    rm -rf /var/cache/apk/* /tmp/* /var/tmp/*; \
	\
# verify that the binary works
	gosu nobody true

RUN mkdir /docker-entrypoint-initdb.d

# install "bash" for docker-entrypoint.sh
# install "pwgen" for randomizing passwords
# install "tzdata" for /usr/share/zoneinfo/
RUN set -ex; \
	apk --update --no-cache add \
		bash \
		pwgen \
		tzdata; \
	rm -rf /var/cache/apk/* /tmp/* /var/tmp/*;

RUN set -ex; \
	apk --update --no-cache add \
    mariadb \
    mariadb-client; \
    rm -rf /var/cache/apk/* /tmp/* /var/tmp/*; \
	\
# comment out any "user" entires in the MySQL config ("docker-entrypoint.sh" or "--user" will handle user switching)
	sed -ri 's/^user\s/#&/' /etc/my.cnf /etc/my.cnf.d/*; \
# purge and re-create /var/lib/mysql with appropriate ownership
	rm -rf /var/lib/mysql; \
	mkdir -p /var/lib/mysql /var/run/mysqld; \
	chown -R mysql:mysql /var/lib/mysql /var/run/mysqld; \
# ensure that /var/run/mysqld (used for socket and lock files) is writable regardless of the UID our mysqld instance ends up having at runtime
	chmod 777 /var/run/mysqld; \
# comment out a few problematic configuration values
	find /etc/my.cnf.d/ -name '*.cnf' -print0 \
		| xargs -0 grep -l -E '^(bind-address|log|skip-networking)' \
		| xargs -rt sed -Ei 's/^(bind-address|log|skip-networking)/#&/'; \
# don't reverse lookup hostnames, they are usually another container
	echo '[mysqld]\nskip-host-cache\nskip-name-resolve' > /etc/my.cnf.d/docker.cnf

VOLUME /var/lib/mysql

# use mariadb's docker-entrypoint.sh
ADD https://raw.githubusercontent.com/docker-library/mariadb/master/docker-entrypoint.sh usr/local/bin/docker-entrypoint.sh
RUN chmod ugo+x usr/local/bin/docker-entrypoint.sh
RUN chown mysql usr/local/bin/docker-entrypoint.sh

# patch docker-entrypoint.sh for alpine linux, because included date-command doesn't support rfc parameter
RUN sed -i s/--rfc-3339=seconds/-I'seconds'/g usr/local/bin/docker-entrypoint.sh

RUN ln -s usr/local/bin/docker-entrypoint.sh / # backwards compat
ENTRYPOINT ["docker-entrypoint.sh"]

EXPOSE 3306
CMD ["mysqld"]
