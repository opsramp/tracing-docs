package handlers

import (
	"go-otel-example/tracing"
	"net/http"

	"github.com/gin-gonic/gin"
	"go.opentelemetry.io/otel/attribute"
)

func Ping(c *gin.Context) {
	_, span := tracing.Tracer.Start(c, "/ping")
	span.SetAttributes(attribute.String("http-lib", "gin"))
	c.JSON(http.StatusOK, gin.H{
		"message": "pong",
	})
	span.End()
}
