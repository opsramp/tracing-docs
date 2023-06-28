package main

import (
	"fmt"

	"math/rand"
	"net/http"

	"github.com/gin-gonic/gin"
	"go.opentelemetry.io/otel"
	"go.opentelemetry.io/otel/attribute"
)

// name is the Tracer name used to identify this instrumentation library.
const tracerName = "gin_gogonic"

func getRand() string {
	return fmt.Sprintf("%d", rand.Intn(6))
}

func main() {
	r := gin.Default()

	tracer := otel.Tracer(tracerName)

	r.GET("/ping", func(c *gin.Context) {
		_, span := tracer.Start(c, "/ping")
		span.SetAttributes(attribute.String("http-lib", "gin"))
		c.JSON(http.StatusOK, gin.H{
			"message": "pong",
		})
		span.End()
	})

	r.GET("/rolldice", func(c *gin.Context) {

		_, span := tracer.Start(c, "/rolldice")

		span.SetAttributes(attribute.String("http-lib", "gin"))

		c.JSON(http.StatusOK, getRand())
		span.End()

	})
	r.Run() // listen and serve on 0.0.0.0:8080
}
