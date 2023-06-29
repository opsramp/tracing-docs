package handlers

import (
	"fmt"
	"go-otel-example/tracing"
	"math/rand"
	"net/http"

	"github.com/gin-gonic/gin"
	"go.opentelemetry.io/otel/attribute"
)

func getRand() string {
	return fmt.Sprintf("%d", rand.Intn(6))
}

func RollDice(c *gin.Context) {

	_, span := tracing.Tracer.Start(c, "/rolldice")

	span.SetAttributes(attribute.String("http.library", "gin"))

	c.JSON(http.StatusOK, getRand())
	span.End()

}
