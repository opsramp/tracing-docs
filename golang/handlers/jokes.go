package handlers

import (
	"fmt"
	"go-otel-example/tracing"
	"math/rand"
	"net/http"
	"strconv"

	"github.com/gin-gonic/gin"
	"go.opentelemetry.io/otel/attribute"
	"go.opentelemetry.io/otel/codes"
	"go.opentelemetry.io/otel/trace"
)

func Jokes(c *gin.Context) {
	// =========== Root Span ===========================
	ctx, rootSpan := tracing.Tracer.Start(c, "/jokes")
	defer rootSpan.End()
	// =================================================

	l := c.DefaultQuery("limit", "1")
	limit, err := strconv.Atoi(l)
	if err != nil {
		// =========================  Setting the Span Status as Error ===============================
		rootSpan.AddEvent(err.Error(), trace.WithAttributes(attribute.String("api.path", "/jokes")))
		rootSpan.SetStatus(codes.Error, "operationThatCouldFail failed")
		// ===========================================================================================
		c.JSON(http.StatusBadRequest, "limit needs to be a number")
		return
	}

	jokes := []string{}
	for index := 0; index < limit; index++ {
		// ====================== Child Span ========================================
		_, span := tracing.Tracer.Start(ctx, fmt.Sprintf("getJoke() - %d", index+1))
		jokes = append(jokes, getJoke())
		span.End()
		// ==========================================================================
	}
	c.JSON(http.StatusOK, jokes)
}

func getJoke() string {

	jokes := []string{
		"Why do bears hate shoes so much? They like to run around in their bear feet.",
		"When Captain Picard's sewing machine broke he brought it to the repairman and said... \"make it sew.\"",
		"Superman and Eyore had a baby. The baby's name? Supereyore",
		"You know what I hate about fashion designers? They are so clothes-minded.",
		"Why did the Fall break off from all the other seasons? Because it wanted autumnomy",
	}

	return jokes[rand.Intn(len(jokes))]
}
